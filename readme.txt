
===================
Adding a subscriber self edit form to the Icegram Email subscriber plugin

A fork of the Icegram Email-subscriber Wordpress plugin
Plugin URL: https://wordpress.org/plugins/email-subscribers/
Plugin Author URL: https://www.icegram.com/

Fork Author: Craig Lambie
Fork Author URL: http://craig.lambie.net.au/2017/12/adding-edit-profile-button-icegram-email-subscriber-emails/
Fork GitHub: https://github.com/cclambie/icegram-es-edit-shortcode/


Basically, by adding a file into the \subscribers folder and switching 3 other files you will get the line
	"If you would like to change your subscription or your details on our system, please click here."
added to your emails, and here will link to a page that a subscriber can edit their Email subscription profile.

To do this:

1. Download the code from GitHub
2. Put the file view-subscriber-edit-pub.php into the folder \subscribers*
3. Replace the file \settings\settings-edit.php with the download
   a. This adds a setting, you need to tell me where the shortcode is installed for the link
4. Replace the file \register\es-sendmail.php
   a. This is so the line will be added on the send of an email
5. Replace the file \email-subscribers.php
   a. This is so the new shortcode is registered
6. Now create a new hidden page and put the shortcode on it
   [email-subscribers-form-edit]
7. Copy the part of the URL after yourdomain.com/thisbit/
   a. Go to your settings page for the ES Plugin: yourdomain.com/wp-admin/admin.php?page=es-settings#admin
   b. note the settings "Edit Subscribe shortcode location" and paste "/thisbit/" there and scroll down and click "save settings
8. Now send a test email newsletter to yourself and check it is all working
   a. if there is a problem, let me know and I can try to fix it

*all files are in your \plugins\email-subscribers\ or child folders