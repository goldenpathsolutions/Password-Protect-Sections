<?php

/*
	Plugin Name: Password Protect Sections
	Plugin URI: http://wordpress.org/extend/plugins/password-protect-sections   
	Description: Password protect sections of content within a post
	Author: Patrick Jackson, Golden Path Solutions <pjackson@goldenpathsolutions.com>
	Version: 0.01
	Author URI: http://www.goldenpathsolutions.com
        License: GPLv2
 */

class Password_Protect_Sections {
    
    function __construct(){
        
        //Handle plugin installation/uninstallation
        register_activation_hook( __FILE__, array( &$this, 'activate' ) );
        register_deactivation_hook( __FILE__, array( &$this, 'deactivate' ) );
        register_uninstall_hook( __FILE__, array( &$this, 'uninstall' ) );
        
        //This plugin uses session variables, so initialize sessions
        add_action('init', array( &$this, 'register_session' ) );
        
        
        //Add a Custom Post Type for Password Objects
        require_once ( 'classes/class-password-post-type.php' );
        $password_post_type = new Password_Custom_Post_Type();
        
        
        //Add shortcode handler
        require_once( 'classes/class-password-shortcode-handler.php' );
        $password_shortcode_handler = new Password_Shortcode_Handler();
        
        
    }
    
    /**
     * Do this when plugin is activated.
     * Create custom database tables/fields here
     */
    public function activate(){
        
    }
    
    /**
     * Do this when plugin is deactivated
     * Do any cleanup required if plugin is deactivated (but not deleted)
     */
    public function deactivate(){
        
    }
    
    /**
     * Do this when plugin is uninstalled
     * Do any cleanup when plugin is deleted
     */
    public function uninstall(){
        
    }
    
    public function register_session(){
        if( !session_id() )
            session_start();
    }
    
}

new Password_Protect_Sections();