<?php

/**
 * Password AJAX Handler
 * 
 * Handles AJAX calls from clients to perform actions
 * 
 * @author Patrick Jackson <pjackson@goldenpathsolutions.com>
 * @copyright (c) 2015, Golden Path Solutions, Inc.
 * @version 1.0.0
 * @since 0.2.0
 * 
 * @package password-protect-sections
 * 
 */

require_once 'class-password-post-type.php';
require_once 'class-password-authenticator.php';
require_once 'class-shortcode-parser.php';

/**
 * Class Password Ajax Handler
 * 
 * @since 0.2.0
 */
class Password_Ajax_Handler {
    
    static $js_version = "1.0.0";
    
    /**
     * @since 0.2.0
     */
    static function init() {
        
        // Register AJAX Handler JavaScript
        add_action('init', array(__CLASS__, 'register_script'));
        add_action('wp_enqueue_scripts', array(__CLASS__, 'enqueue_script'));
        
        
        // add get_password_protected_content action for logged-in users
        add_action( 'wp_ajax_get_password_protected_content', 
                array( __CLASS__, 'get_password_protected_content') );
        
        // add get_password_protected_content action for non-logged-in visitors
        add_action( 'wp_ajax_nopriv_get_password_protected_content', 
                array(  __CLASS__, 'get_password_protected_content') );
    }
    
    /**
     * @since 0.2.0
     */
    public static function enqueue_script(){
        wp_enqueue_script('gps-password-ajax-handler');
    }
    
    /**
     * @since 0.2.0
     */
    public static function dequeue_script(){
        wp_dequeue_script('gps-password-ajax-handler');
    }
    
    /**
     * @since 0.2.0
     */
    public static function register_script(){
        wp_register_script( 'gps-password-ajax-handler', 
                plugins_url('password-protect-sections/js/ajax-handler.js'), 
                array('jquery'), self::$js_version, true);
        
        // make the ajax_url available to the ajax javascript
	wp_localize_script( 'gps_password_ajax_handler', 'gps_ajax_data',
            array( 'ajax_url' => admin_url( 'admin-ajax.php' ) ) );
    }
    
    /**
     * Called by AJAX Handler to return the protected content
     */
    public static function get_password_protected_content(){
        
        $content = '';
        
        $password_post = Password_Post_Type::get_password_post_by_name(filter_input(INPUT_POST, 'password-name' ));
        
        $authenticator = new Password_Authenticator($password_post);
        $is_authenticated = $authenticator->is_authenticated(
                filter_input(INPUT_POST, 'gps-section-password' ));
        
        if ($is_authenticated){
            
            $protected_content_post = get_post( filter_input(INPUT_POST, 'protected-content-post-id' ) );
            
            $parser = new Shortcode_parser($protected_content_post->post_content, 
                    'gps-password');
            
            /*
             * pull content contained by shortcode, and apply any shortcodes
             * that content contains
             */
            $content_array = do_shortcode( $parser->get_shortcode_content() );
            
            // wrap the content with the template for unlocked state
            $template_file = Password_Template_Handler::find_template_file ( 
                    "/password-protect-sections-unlocked-template.php" );
            
            /*
             * Store the contents of the template in the $content variable.
             * We assume this will include the original contents of the $content
             * variable.  The template should control what is finally output.
             * 
             * template file expects a $content variable with the content contained
             * by the shortcode, and a $password_post variable with the password
             * custom post type object.
             */
            $output = array();
            foreach( $content_array as $content ){
                ob_start();

                include($template_file);

                array_push( $output, ob_get_contents() );

                ob_end_clean();
            }
            
            // place the content in a json array and output
            echo json_encode( array( "content"=>$output ) );
            
        } else {
            
            echo json_encode( array ("error" => $authenticator->error ) );
        }
        
        wp_die();
    }
    
    
}