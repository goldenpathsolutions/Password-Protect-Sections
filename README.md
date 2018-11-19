Password Protect Sections
=========================

A WordPress Plugin that allows a user to password protect sections of content within a post using a simple shortcode.

# Disclaimer - Beta Release!

Please note that this is a beta release of this plugin.  It has had limited use in production environments, and so we advise caution and careful testing when using it on your site.  The purpose of releasing it at this time is to gather feedback from users to improve robustness, and prioritize additional features. We encourage you to post issues in the plugin's WordPress.org Support Forum, [GitHub Repository](https://github.com/goldenpathsolutions/Password-Protect-Sections), or visit the plugin page on our website.

# Introduction

The purpose of this plugin is to allow sections within a WordPress post to be protected by a shared, reusable password object.  Following is a list of features.

* __No Account Necessary__ - Access control by a single password.
* __AJAX Support__ - page does not have to refresh to expose protected content.
* __Reusable Passwords__ - the same password may be used to unlock many different sections of content.
* __Remember For Session__ - a visitor only needs to enter the password once during a visit.
* __Customizable Password Content and Messages__ - site administrators can easily enter their own error messages and content to control the presentation of password protected sections when locked.
* __Customizable Templates__ - developers can add their own custom templates to the theme to control the layout and behavior of password objects.


## No Account Necessary
Password protection is usually accomplished by allowing content to be viewed only when the visitor is logged in to a WordPress account, or by entering the password for a single page.  In this case, we use a reusable Password custom post type that may be applied to any content across the site.  When a visitor enters a Password object's password, all content protected by that password will be unlocked for the duration of the visit.  Users can relock protected content at any time.

## AJAX Support
When a user unlocks the protected section of content, AJAX may be used to authenticate the password and pull in the protected content.  This avoids having to reload the entire page.  Currently, there are limitations

## Reusable Passwords
Rather than making a user enter a new password for each new section of content to be protected, the same password may be applied to many sections of content.  There may be many such passwords created, being distinguished in the shortcode using a _Password ID_.

## Remember For Session
When a visitor unlocks a password, the event is stored in the session so they do not have to enter the password again during their visit.

## Customizable Password Content and Messages
Site administrators may edit the content of the Password custom post type.  By default, this content is displayed above the password field.  This allows administrators to easily customize the locked state of the protected content.  There are also custom fields for editing the error message when an incorrect password is entered.

## Customizable Templates
More advanced users can create their own templates to add to their theme.  This allows them a great deal of control over the locked and unlocked state of the protected content.  Currently, you can only create one template that is used by all password objects.

# Instructions

## Installation
No special installation instructions.  Just install the plugin like any other. :)

## Create a _Password_ post
The first thing to do is Add a new Password object.  A Password is a custom post type, so you can create as many as you want.  You create them just like you would a new page or post.  All you really need is a unique title for the Password to get started. Later, you can add custom content and templates if you want to get fancy.

![Screenshot: Editing a Password Object](http://www.goldenpathsolutions.com/live/wp-content/uploads/2015/08/Screenshot-2015-08-25-07.49.17.png)

## Using the Shortcode
The next step is to enclose some section of content with the shortcode.  The shortcode must include the title of the Password object you want to use.

    This section is above the protected section. It is NOT password protected
    [gps-password title='My First Password']
    This section IS password protected.
    [/gps-password]
    This section is below the protected section. It is NOT password protected



# Contact
Patrick Jackson
Golden Path Solutions, Inc.
https://goldenpathsolutions.com
patrick@goldenpathsolutions.com
Twitter: @GoldenPathSolns
