<?php

/**
 * Password AJAX Handler
 * 
 * Handles AJAX calls from clients to perform actions
 * 
 * @author Patrick Jackson <pjackson@goldenpathsolutions.com>
 * @copyright (c) 2015, Golden Path Solutions, Inc.
 * @version 1.0.1
 * @since 0.2.0
 * 
 * @package password-protect-sections
 * 
 */

namespace gps\password_protect_sections;

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
     * Initialize this plugin's ajax actions
     * 
     * @since 0.2.0
     */
    public static function init() {
        
        // Register AJAX Handler JavaScript
        add_action('init', array(__CLASS__, 'register_script'));
        add_action('wp_enqueue_scripts', array(__CLASS__, 'enqueue_script'));
        
        
        // add get_password_protected_content action for logged-in users
        add_action( 'wp_ajax_get_password_protected_content', 
                array( __CLASS__, 'get_password_protected_content') );
        
        // add get_password_protected_content action for non-logged-in visitors
        add_action( 'wp_ajax_nopriv_get_password_protected_content', 
                array(  __CLASS__, 'get_password_protected_content') );
        
        // add get_password_form action for logged-in users
        add_action( 'wp_ajax_get_password_form', 
                array( __CLASS__, 'get_password_form') );
        
        // add get_password_form action for non-logged-in visitors
        add_action( 'wp_ajax_nopriv_get_password_form', 
                array(  __CLASS__, 'get_password_form') );
        
    }
    
    /**
     * Enqueue the script used for handling this plugin's ajax
     * 
     * @since 0.2.0
     */
    public static function enqueue_script(){
        wp_enqueue_script('gps-password-ajax-handler');
    }
    
    /**
     * Dequeue the script used for this plugin's ajax.  This is called when
     * ajax is disabled in the shortcode by setting the <code>ajax</code> attribute
     * to false.
     * 
     * @since 0.2.0
     */
    public static function dequeue_script(){
        wp_dequeue_script('gps-password-ajax-handler');
    }
    
    /**
     * Register the script used to handle ajax for this plugin
     * 
     * @since 0.2.0
     */
    public static function register_script(){
        wp_register_script( 'gps-password-ajax-handler', 
                plugins_url('password-protect-sections/js/ajax-handler.js'), 
                array('jquery'), self::$js_version, true);
        
        // make the ajax_url available to the ajax javascript
	wp_localize_script( 'gps-password-ajax-handler', 'gps_ajax_data',
            array( 
                'ajax_url' => admin_url( 'admin-ajax.php'),
                'ajax_loader_url' => plugins_url() . "/password-protect-sections/images/ajax-loader.gif" 
            ));
    }
    
    /**
     * Called by AJAX Handler JS to return the protected content when unlocking.
     * Returns unlocked template.  Authenticates password.
     * 
     * 
     * @since 0.2.0
     */
    public static function get_password_protected_content(){
                        
        $password_post = Password_Post_Type::get_password_post_by_name(
                filter_input(INPUT_POST, 'password-name' ));
        
        $authenticator = new Password_Authenticator( $password_post );
        
        $is_authenticated = $authenticator->set_authenticated(
                filter_input(INPUT_POST, 'gps-section-password' ));
        
        
        // this is going to be the array sent back in JSON format
        $response = array();
        
        if ($is_authenticated){
            
            // handle $is_reload_page = true - don't bother pulling the content
            $is_reload_page = filter_input(INPUT_POST, 'is-reload-page');
            
            
            if ( '1' === $is_reload_page ){
                
                echo json_encode( array("success" => "No Content: reload page selected"));
                
                wp_die();
            }
            
            // if $is_reload_page is false, go ahead and pull the content
            self::write_content($password_post, $is_reload_page);
            
        } else {
            
            $response["error"] = $authenticator->get_error();
            
            echo json_encode( $response );
        }
        
        wp_die();
    }
    
    /**
     * Get Password Form
     * 
     * Called by Ajax Handler JS to get the password form (locked template)
     * when re-locking protected content.
     * 
     * 
     * @since 0.2.0
     */
    public static function get_password_form(){
        
        // used to add the relevant post id in the template
        $protected_post_id = filter_input(INPUT_POST, 'protected-post-id' );
        
        // used in the template to indicate whether reload_page attribute
        $is_reload_page = filter_input(INPUT_POST, 'is-reload-page');
        
        // used by $template_file
        $password_post = Password_Post_Type::get_password_post_by_name(
                filter_input(INPUT_POST, 'password-name' ));
        
        // first, check the nonce.  If that fails, assume the submitted post
        // was for another password form on the page (or it's a hacker), and
        // ignore the submission.
        if ( ! wp_verify_nonce( filter_input(INPUT_POST, '_wpnonce'), 
                'relock_protected_section_'.$password_post->ID ) ){
            wp_die();
        }
        
        // use authenticator to set session variables
        $authenticator = new Password_Authenticator($password_post);
        $authenticator->set_authenticated( false );
        
        // wrap the content with the template for unlocked state
        $template_file = Password_Template_Handler::find_template_file ( 
                "/password-protect-sections-locked-template.php" );
        
        ob_start();

        include($template_file);
        
        $output = json_encode( array( "content" => ob_get_contents() ) );

        ob_end_clean();
        
        echo $output;
        
        wp_die();
        
    }
    
    
    /**
     * Gets the content for this post and writes it to content array in JSON format,
     * 
     * @access private
     * @param WP_Post $password_post the password post type object.  Passed to the
     *                              template.
     * @param boolean $is_reload_page true when reload-page is set.  Passed to the
     *                                template.
     * @since 0.2.0
     */
    private static function write_content($password_post, $is_reload_page){
        
        $protected_post_id = filter_input(INPUT_POST, 'protected-post-id' );
        $protected_post = get_post( $protected_post_id );

        $parser = new Shortcode_parser($protected_post->post_content, 
                'gps-password', $password_post->post_title);

        /*
         * pull content contained by the $password_post's shortcode.
         * It's in an array because the same password may be used multiple
         * times on the same page.
         */
        $content_array = $parser->get_shortcode_content();

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
        
        $response = array();
        $response["content"] = $output;

        // place the content in a json array and output
        echo json_encode( $response );
    }
    
}