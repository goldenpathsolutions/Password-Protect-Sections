<?php

/**
 * 
 * Plugin Name: Password Protect Sections
 * Plugin URI: http://wordpress.org/extend/plugins/password-protect-sections   
 * Description: Password protect sections of content within a post
 * Author: Patrick Jackson, Golden Path Solutions <pjackson@goldenpathsolutions.com>
 * Version: 0.2.1
 * Author URI: http://www.goldenpathsolutions.com
 * License: GPLv2
 * 
 * 
 * @package password-protect-sections
 * @author Patrick Jackson, Golden Path Solutions <pjackson@goldenpathsolutions.com>
 * @version 0.2.1
 * 
 */

class Password_Protect_Sections {
    
    function __construct(){
        
        //Handle plugin installation/uninstallation
        register_activation_hook( __FILE__, array( __CLASS__, 'activate' ) );
        register_deactivation_hook( __FILE__, array( __CLASS__, 'deactivate' ) );
        register_uninstall_hook( __FILE__, array( __CLASS__, 'uninstall' ) );
        
        // This plugin uses session variables, so initialize sessions
        add_action('init', array( __CLASS__, 'register_session'), 1 );
        
        
        // Load font awesome if something else hasn't already done so
        add_action('wp_enqueue_scripts', array( __CLASS__, 'check_font_awesome'), 99999);
        
        
        //Add a Custom Post Type for Password Objects
        require_once ( 'classes/class-password-post-type.php' );
        $password_post_type = new Password_Post_Type();
        
        
        //Add shortcode handler
        require_once( 'classes/class-password-shortcode-handler.php' );
        $password_shortcode_handler = new Password_Shortcode_Handler();
        
        //Add settings page
        require_once( 'classes/class-password-settings-handler.php');
        $password_settings_handler = new Password_Settings_Handler();
        
        
    }
    
    /**
     * Do this when plugin is activated.
     * Create custom database tables/fields here
     */
    public static function activate(){
        
    }
    
    /**
     * Do this when plugin is deactivated
     * Do any cleanup required if plugin is deactivated (but not deleted)
     */
    public static function deactivate(){
        
    }
    
    /**
     * Do this when plugin is uninstalled
     * Do any cleanup when plugin is deleted
     */
    public static function uninstall(){
        
    }
    
    public static function register_session(){
        
        if( !session_id() ){
            session_start();
        }
    }
    
    /**
     * We want to use font awesome for something, but don't want to cause
     * conflicts, so check to make sure something else hasn't loaded it already.
     * 
     * Thanks G.M.!
     * http://wordpress.stackexchange.com/questions/121273/how-to-check-if-a-stylesheet-is-already-loaded
     */
    public static function check_font_awesome() {
        
      global $wp_styles;
      
      $srcs = array_map('basename', (array) wp_list_pluck($wp_styles->registered, 'src') );
      
      if ( ! ( in_array('font-awesome.css', $srcs) || in_array('font-awesome.min.css', $srcs) )  ) {
        wp_enqueue_style('font-awesome', plugins_url() . '/password-protect-sections/css/font-awesome.min.css' );
      }
      
    }
    
}

new Password_Protect_Sections();