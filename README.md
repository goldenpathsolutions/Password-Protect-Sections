Password-Protect-Sections
=========================

A WordPress Plugin that allows a user to password protect sections of content within a post using a simple shortcode.

#Introduction

The purpose of this plugin is to allow sections within a WordPress post to be protected by a password.  Following is a list of features offed by this plugin.

* __No Account Necessary__ - Access control by password and/or account login.
* __AJAX Support__ - page does not have to refresh to expose protected content.
* __Reusable Passwords__ - the same password may be used to unlock many different sections of content.
* __Remember Me__ - a visitor only needs to enter the password once during a visit.  Optionally, they may use a cookie to remember them for future visits.


##No Account Necessary
Password protection is usually accomplished by allowing content to be viewed only when the visitor is logged in to a WordPress account.  While this method of access control is supported, it is not required.  Any visitor that enters the correct password will unlock the protected content.  The protected section may also be configured to automatically allow logged-in users to view it.

##AJAX Support
When a user unlocks the section of content for the first time, AJAX is used to authenticate the password and populate the web page with the protected content.  The avoids having to reload the entire page.

##Reusable Passwords
Rather than making a user enter a new password for each new section of content to be protected, the same password may be applied to many sections of content.  There may be many such passwords created, being distinguished in the shortcode using a _Password ID_.

##Remember Me Feature
When a visitor unlocks a password, the event is stored in the session so that they do not have to enter the password again during their visit.  The visitor may optionally check a Remember Me box, in which case the system will store a cookie on the user's computer so that the user won't have to enter the password on future visits.

This feature may be customized in the password's settings.
