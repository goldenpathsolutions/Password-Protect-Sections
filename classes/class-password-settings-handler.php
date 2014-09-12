<?php

/* 
 * Password Settings Handler
 * 
 * Description: Controller for Password Protected Sections settings page.  It is
 * Repsonsible for storing and retrieving settings selections in the database.
 * 
 * Author: Patrick Jackson, Golden Path Solutions
 * Author URL: http://www.goldenpathsolutions.com
 * Created: 2014-07-21
 */

class Password_Settings_Handler {
    
    function __construct(){
        add_action('admin_menu' , array( &$this, 'add_settings_to_post_menu' ) );

        
    }
    
    public function add_settings_to_post_menu() {
        add_submenu_page('edit.php?post_type=gps_password', 'Password Settings', 'Settings', 'publish_password', 'gps_password_settings', array( &$this, 'gps_password_settings_view') );
    }
    
    public function gps_password_settings_view(){
        
        //load the view for this page
        wp_nonce_field( 'gps_password_settings_save' );
        require_once (dirname( __FILE__ ) . '/../views/password-settings-view.php');
    }
    
}