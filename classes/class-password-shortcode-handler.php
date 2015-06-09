<?php
/** 
 * Password Shortcode Handler
 * 
 * This class manages the shortcode used by this plugin
 * 
 * <code>[gps-password title='<title of password>' use-ajax='no'] Protected Content [/gps-password]</code>
 * 
 * @author Patrick Jackson <pjackson@goldenpathsolutions.com>
 * @copyright (c) 2014, Golden Path Solutions, Inc.
 * @link http://www.goldenpathsolutions.com
 * @version 1.1.0
 * @since 0.1.4
 *
 * @package password-protect-sections
 * 
 */

require_once 'class-password-ajax-handler.php';
require_once 'class-password-template-handler.php';

/**
 * @since 0.1.4
 */
class Password_Shortcode_Handler {
    
    static $style_version = "1.0.1";
    
    /**
     * @since 0.1.4
     */
    function __construct() {
        
        // register and enqueue the style
        add_action('init', array(__CLASS__, 'register_style'));
        add_action('wp_enqueue_scripts', array(__CLASS__, 'enqueue_style'));
        
        /*** Add AJAX handler ***/
        Password_Ajax_Handler::init();
        
        // Add AJAX Handler enqueue operation to the wp_enqueue_scripts hook
        add_action('wp_enqueue_scripts', array('Password_Ajax_Handler', 'enqueue_script'));
        
        // Add the shortcode
        add_shortcode( 'gps-password', array( __CLASS__, 'gps_password_shortcode') );
         
    }
    
    /**
     * Enqueue Style
     * 
     * Enqueue the css for this plugin
     * 
     * @since 0.2.0
     */
    public static function register_style(){
        
        wp_register_style("gps_password_style", 
                plugins_url('password-protect-sections/css/style.css'), null, 
                self::$style_version);
    }
    
    /**
     * @since 0.1.4
     */
    public static function enqueue_style(){
        wp_enqueue_style('gps_password_style');
    }
    
    
        
    /**
     * GPS Password Shortcode
     * 
     * Starting point for all the work done by this shortcode
     * 
     * @param array $attributes
     *  @type   string  title  title of the password used for this section
     *  @type   boolean ajax   use ajax to validate password and update 
*                              protected content when true [default] ('true','yes'),
*                              don't use ajax when false ('false','no')
     * @since 0.1.0
     */
    public static function gps_password_shortcode( $attributes_in, $content = null ){
        
        
        $is_authenticated = false;
        $password_entered = false;
        
        $atts = shortcode_atts( array(
            'title' => null,
            'ajax' => true,
        ), $attributes_in );
        
        // get the password object (custom post type = 'gps_password')
        $password_post = get_page_by_title( $atts['title'], null, 'gps_password' );
        
        // if we can't find a password, then don't try to protect the content
        if ( ! isset( $password_post ) )  return do_shortcode($content);
        
        // dequeue the ajax script if use-ajax is set to 'false' or 'no'
        $ajax = strtolower( $atts['ajax'] );
        if ( $ajax === 'false' || $ajax === 'no' ) {
            wp_dequeue_script( 'gps-password-ajax-handler' );
        }
       
        /*
         * Note: much of the following code is used in the case where ajax=no or
         * there is no client-side javaScript.
         */
        
        // check to see if we're relocking
        $relock_protected_section = filter_input( INPUT_POST, 'relock-protected-section' );
        if ( $relock_protected_section ){
            
            //verify the nonce
            if ( wp_verify_nonce( filter_input(INPUT_POST, '_wpnonce'), 
                    'relock-protected-section-'.$password_post->ID )){
                
                unset( $_SESSION['gps_password_' . $password_post->ID . 
                    '_authenticated'] );
            }
        }
                        
        // check to see if we're handling a password submission
        $gps_section_password = filter_input(INPUT_POST, 'gps-section-password' );
        
        /*
         * Make sure password entered was for this password object.
         * Handles case where different password shortcodes are on one page.
         * To do this, handle the nonce.  If it returns false, assume
         * the password in the form submission is irrelevant for this password
         * shortcode, and ignore it.
         */
        if ( !wp_verify_nonce( filter_input( INPUT_POST, '_wpnonce' ), 
                'unlock_protected_section_'.$password_post->ID )){
            $gps_section_password = null;
        }
        
        // if we're handling a form submission...
        if ( isset( $gps_section_password ) ) {
            
            $password_entered = true;  // superfluous
            
            // set the authentication session variables via the authenticator
            $authenticator = new Password_Authenticator($password_post);
            $is_authenticated = $authenticator->is_authenticated( $gps_section_password );
                    
        // otherwise, if there is a session variable that says this password section is unlocked...
        } else if ( isset( $_SESSION['gps_password_' . $password_post->ID . '_authenticated'] ) ) {
            $is_authenticated = true;
        }        
                
        // decide whether to return the protected content or the login form
        return $hide_content ? self::get_replacement_content( $password_post, 
                $content, $password_entered && !$is_authenticated, $is_authenticated ) 
                : do_shortcode( self::get_replacement_content( $password_post, 
                        $content, false, $is_authenticated) );
          
    }
    
    
    /**
     * Get Replacement Content
     * 
     * Decides what content should replace the shortcode: either the protected
     * content, or the login form.
     * 
     * 
     * @global boolean $password_failed
     * @access private
     * @param {object} $password_post   The gps_password custom post type
     * @param string $content                   The protected content
     * @param boolean $password_failed          True when the password does not 
     *                                          match the gps_password
     * @param boolean $unlocked                 True when gps_password is unlocked,
     *                                          otherwise false
     * @return string                           The protected content when $unlocked 
     *                                          is true, otherwise the login form
     */
    private static function get_replacement_content( $password_post, $content = null, 
            $password_failed, $unlocked ){
        
        global $password_failed;
        
        if ( isset( $_SESSION['gps_password_' . $password_post->ID . '_failed'] ) 
                && $_SESSION['gps_password_' . $password_post->ID . '_failed'] ){
            
            $password_failed = true;
        }
        
        ob_start();
        
        if ( $unlocked ){ 
            $template_file = Password_Template_Handler::find_template_file( 
                    "/password-protect-sections-unlocked-template.php" );
        } else {
            $template_file = Password_Template_Handler::find_template_file ( 
                    "/password-protect-sections-locked-template.php" );
        }
        
        require( $template_file );
        
        // clear the password failed session var so it only shows once.
        if ( $password_failed ) {
            $_SESSION['gps_password_' . $password_post->ID . '_failed'] = false;
        }
        
        return ob_get_clean();
        
    }
    
}