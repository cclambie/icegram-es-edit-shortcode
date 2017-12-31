<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit; 
}

function CreateEditURL($did) {

	$nonce = wp_create_nonce( 'update-es-subscriber'.$did );
	$url = site_url();
	$page = get_option('ig_es_shortcodepage');
	
	$url = $url.$page.'?did='.$did.'&nonce='.$nonce;
	
	return $url;

}

function es_af_shortcode_edit() {

	global $wpdb;
	
	$did = isset($_GET['did']) ? $_GET['did'] : '0';
	
	//echo '<a href="' . CreateEditURL($did) . '">here</a>';
	
	//check nonce
	wp_verify_nonce( $_GET['nonce'], 'update-es-subscriber'.$did );


?>

<div class="wrap">
	<?php

	$es_error_found = FALSE;
	
	es_cls_security::es_check_number($did);

	// First check if ID exist with requested ID
	$result = es_cls_dbquery::es_view_subscriber_count($did);
	if ($result != '1') {
		?><div class="error fade">
			<p><strong>
				<?php echo __( 'Oops, selected details does not exists.', ES_TDOMAIN ); ?>
			</strong></p>
		</div><?php
	} else {
		$es_errors = array();
		$es_success = '';
		$es_error_found = FALSE;

		$data = array();
		$data = es_cls_dbquery::es_view_subscriber_search("", $did);

		// Preset the form fields
		$form = array(
			'es_email_name' => stripslashes($data[0]['es_email_name']),
			'es_email_mail' => $data[0]['es_email_mail'],
			'es_email_status' => $data[0]['es_email_status'],
			'es_email_group' => $data[0]['es_email_group'],
			'es_email_id' => $data[0]['es_email_id']
		);
	}

	// Form submitted, check the data
	if (isset($_POST['es_form_submit']) && $_POST['es_form_submit'] == 'yes') {

		// Just security thingy that wordpress offers us
		//check_admin_referer('es_form_edit');

		$form['es_email_status'] = isset($_POST['es_email_status']) ? $_POST['es_email_status'] : '';
		$form['es_email_name'] = isset($_POST['es_email_name']) ? $_POST['es_email_name'] : '';
		$form['es_email_mail'] = isset($_POST['es_email_mail']) ? $_POST['es_email_mail'] : '';

		if ($form['es_email_mail'] == '') {
			$es_errors[] = __( 'Please enter your email address.', ES_TDOMAIN );
			$es_error_found = TRUE;
		}

		$form['es_email_group'] = isset($_POST['es_email_group']) ? $_POST['es_email_group'] : '';
		$form['es_email_id'] = isset($_POST['es_email_id']) ? $_POST['es_email_id'] : '0';

		if($form['es_email_group'] != "") {
			$special_letters = es_cls_common::es_special_letters();
			if (preg_match($special_letters, $form['es_email_group'])) {
				$es_errors[] = __( 'Error: Special characters are not allowed in the group name.', ES_TDOMAIN );
				$es_error_found = TRUE;
			}
		}

		//	No errors found, we can add this Group to the table
		if ($es_error_found == FALSE) {	
			$action = "";
			$action = es_cls_dbquery::es_view_subscriber_ins($form, "update");
			if($action == "sus") {
				$es_success = __( 'Your details updated.', ES_TDOMAIN );
			} elseif($action == "ext") {
				$es_errors[] = __( 'Your already exists for this group.', ES_TDOMAIN );
				$es_error_found = TRUE;
			}
		}
	}

	if ($es_error_found == TRUE && isset($es_errors[0]) == TRUE) {
		?><div class="error fade">
			<p><strong>
				<?php echo $es_errors[0]; ?>
			</strong></p>
		</div><?php
	}

	if ($es_error_found == FALSE && strlen($es_success) > 0) {
		?><div class="notice notice-success is-dismissible">
			<p><strong>
				<?php echo $es_success; ?>
			</strong></p>
		</div><?php
	}

	?>

	<style type="text/css">
	.form-table th {
		width:260px;
	}
	</style>

	<div class="wrap">
		<h2>
			<?php //echo __( 'Edit Subscriber', ES_TDOMAIN ); ?>
		</h2>
		<form name="form_addemail" method="post" action="#" onsubmit="return _es_addemail()">
			<div class="tool-box">
				<table class="form-table">
					<tbody>
						<tr>
							<th scope="row">
								<label for="tag-image">
									<?php echo __( 'Full Name', ES_TDOMAIN ); ?>
								</label>
							</th>
							<td>
								<input type="text" name="es_email_name" id="es_email_name" value="<?php echo $form['es_email_name']; ?>" maxlength="225" size="30" />
							</td>
						</tr>
						<tr>
							<th scope="row">
								<label for="tag-image">
									<?php echo __( 'Email Address', ES_TDOMAIN ); ?>
								</label>
							</th>
							<td>
								<input type="text" name="es_email_mail" id="es_email_mail" value="<?php echo $form['es_email_mail']; ?>" maxlength="225" size="30" />
							</td>
						</tr>
						 
						<tr>
							<th scope="row">
								<label for="tag-display-status">
									<?php echo __( 'Categories subscribed', ES_TDOMAIN ); ?>
								</label>
							</th>
							<td>
								<select name="es_email_group" id="es_email_group">
									<option value=''><?php echo __( 'Select', ES_TDOMAIN ); ?></option>
									<?php
									$thisselected = "";
									$groups = array();
									$groups = es_cls_dbquery::es_view_subscriber_group();
									if(count($groups) > 0) {
										$i = 1;
										foreach ($groups as $group) {
											if(stripslashes($group["es_email_group"]) == $form['es_email_group']) { 
												$thisselected = 'selected="selected"' ; 
											}
											?>
											<option value="<?php echo esc_html(stripslashes($group["es_email_group"])); ?>"	<?php echo $thisselected; ?>>
												<?php echo esc_html(stripslashes($group["es_email_group"])); ?>
											</option>
											<?php
											$thisselected = "";
										}
									}
									?>
								</select>
							</td>
						</tr>
					</tbody>
				</table>
			</div>
			<input type="hidden" name="es_form_submit" value="yes"/>
			<input type="hidden" name="es_email_id" id="es_email_id" value="<?php echo $form['es_email_id']; ?>"/>
			<p style="padding-top:5px;">
				<input class="button-primary" value="<?php echo __( 'Update', ES_TDOMAIN ); ?>" type="submit" />
			</p>
			<?php wp_nonce_field('es_form_edit'); ?>
		</form>
	</div>
</div>

<?php } //end function  ?>