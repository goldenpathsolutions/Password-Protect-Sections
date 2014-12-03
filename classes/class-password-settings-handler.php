<?php

/** 
 * Password Settings Handler
 * 
 * Description: Controller for Password Protected Sections settings page.  It is
 * Repsonsible for storing and retrieving settings selections in the database.
 * 
 * @category password-protect-sections
 * 
 * Author: Patrick Jackson, Golden Path Solutions
 * Author URL: http://www.goldenpathsolutions.com
 * Created: 2014-07-21
 */

include_once ( dirname( dirname( __FILE__ ) ) . '/password-protect-sections.php' );

class Password_Settings_Handler {
    
    function __construct(){
        
        add_action('admin_menu' , array( 'Password_Settings_Handler', 'add_settings_to_post_menu' ) );
        
        add_action('admin_enqueue_scripts', array( 'Password_Settings_Handler', 'enqueue_style') );
        
        //Load font awesome if something else hasn't already done so
        add_action('admin_enqueue_scripts', array( 'Password_Protect_Sections', 'check_font_awesome'), 99999);
    }
    
    public static function enqueue_style(){
        // wp_enqueue_style('gps-admin-style', plugins_url('password-protect-sections/css/style-admin.css'), array(), '1.0.0' );
    }
    
    public static function add_settings_to_post_menu() {
        add_submenu_page('edit.php?post_type=gps_password', 'Password Settings', 'Settings', 'publish_password', 'gps_password_settings', array( 'Password_Settings_Handler', 'gps_password_settings_view') );
    }
    
    public static function gps_password_settings_view(){
        
        //load the view for this page
        wp_nonce_field( 'gps_password_settings_save' );
        require_once (dirname( __FILE__ ) . '/../views/password-settings-view.php');
    }
}