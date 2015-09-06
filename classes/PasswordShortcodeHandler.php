<?php
/** 
 * Password Shortcode Handler
 * 
 * This class manages the shortcode used by this plugin
 * 
 * <code>[gps-password title='<title of password>' use-ajax='no'reload-page='no'] Protected Content [/gps-password]</code>
 * 
 * @author Patrick Jackson <pjackson@goldenpathsolutions.com>
 * @copyright (c) 2014-2015, Golden Path Solutions, Inc.
 * @link http://www.goldenpathsolutions.com
 * @version 1.1.1
 * @since 0.1.0
 *
 * @package password-protect-sections
 * 
 */

namespace gps\password_protect_sections;

require_once 'class-password-ajax-handler.php';
require_once 'class-password-template-handler.php';
require_once 'class-password-authenticator.php';

/**
 * @since 0.1.4
 */
class Password_Shortcode_Handler {
    
    static $style_version = "1.0.2";
    
    /**
     * Keeps track of how many password fields appear on a page
     * so they can be given unique identifiers.  Used for ajax
     * loading. 
     * 
     * @var gps\password_protect_section\PasswordTracker   
     */
    static $password_tracker;
    
    /**
     * @since 0.1.4
     */
    public function __construct() {
        
        static::$password_tracker = new gps\password_protect_section\PasswordTracker();
        
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
     * 
     * @param array $attributes_in  Attributes passed from the shortcode
     *  @type   string title  title of the password used for this section
     *  @type   string ajax   use ajax to validate password and update 
     *                         protected content when true [default] ('true','yes'),
     *                         don't use ajax when false ('false','no')
     *  @type   string reload_page when 'true' or 'yes', reload the page after
     *                              authenticating the password via ajax
     * @param string $content  Content of the password post type submitted
     * 
     * @since 0.1.0
     */
    public static function gps_password_shortcode( $attributes_in, $content = null ){
                        
        $attributes = self::get_attributes( $attributes_in );
        
        // get the password object (custom post type = 'gps_password')
        $password_post = get_page_by_title( $attributes['title'], null, 'gps_password' );
        
        // if we can't find a password, then don't try to protect the content
        if ( ! isset( $password_post ) )  {
            return do_shortcode($content);
        }
        
        self::handle_ajax_attribute( $attributes );
        
        // check to see if we're relocking
        self::handle_relocking_case( $password_post );
        
        // get the password entered. null if nonce verification fails
        $gps_section_password = self::get_password_entered( $password_post );
        
        // if we're handling a form submission, authenticate it
        $is_authenticated = self::authenticate_password( $gps_section_password, 
                $password_post );
        
        // password fails if there is a password, but it wasn't authenticated
        $password_failed = isset( $gps_section_password ) && false !== $is_authenticated;
        
        return do_shortcode( self::get_replacement_content( $password_post, 
                $password_failed, $is_authenticated, $attributes, $content) );
    }
    
    /**
     * Sets global variables that are in a more useful form for the plugin
     * 
     * @access private
     * @param array $attributes_in The attributes from the shortcode
     * 
     * @return array $attributes contains the cleaned attributes
     *  @type boolean ajax True if ajax to be used
     *  @type boolean reload_page True if Reload Page selected
     * @since 0.2.0
     */
    private static function get_attributes( $attributes_in ){
                
        $attributes_out = shortcode_atts( array(
            'title' => null,
            'ajax' => true,
            'reload_page' => false,
        ), $attributes_in );
        
        // set ajax flag to false if attribute is "false" or "no", otherwise true (default)
        $ajax = strtolower( trim( $attributes_out['ajax'] ) );
        $attributes_out['ajax'] = ! ($ajax === 'false' || $ajax === 'no' );
        
        // set Reload Page flag to true if attribute is "true" or "no", otherwise false (default)
        $reload_page = strtolower( trim( $attributes_out['reload_page'] ) );
        $attributes_out['reload_page'] = ($reload_page === 'true' || $reload_page === 'yes');
                
        return $attributes_out;
    }
    
    
    /**
     * Get Replacement Content
     * 
     * Decides what content should replace the shortcode: either the protected
     * content, or the login form.
     * 
     * 
     * @access private
     * @param WP_Post   $password_post      The gps_password custom post type
     * @param boolean   $password_failed    True when the password does not match 
     *                                      the gps_password
     * @param boolean   $unlocked           True when gps_password is unlocked,
     *                                      otherwise false
     * @param array     $attributes         The processed attribute values passed by 
     *                                      the shortcode
     * @param string    $content            The protected content
     * @return string                       The protected content when $unlocked is 
     *                                      true, otherwise the login form
     * 
     * @since 0.1.4
     */
    private static function get_replacement_content( $password_post, 
            $password_failed, $unlocked, $attributes, $content = null ){
                
        ob_start();
        
        if ( $unlocked ){ 
            $template_file = Password_Template_Handler::find_template_file( 
                    "/password-protect-sections-unlocked-template.php" );
        } else {
            $template_file = Password_Template_Handler::find_template_file ( 
                    "/password-protect-sections-locked-template.php" );
        }
        
        require( $template_file );
        
        return ob_get_clean();
        
    }
    
    /**
     * Determine whether user chose to relock content, and handle it if they did
     * 
     * @param   WP_Post $password_post  The password object we are checking
     * @since 0.2.1
     */
    private static function handle_relocking_case( $password_post ){
        
        $relock_protected_section = filter_input( INPUT_POST, 'relock-protected-section' );
        if ( $relock_protected_section ){
            
            //verify the nonce
            if ( wp_verify_nonce( filter_input(INPUT_POST, '_wpnonce'), 
                    'relock_protected_section_'.$password_post->ID )){
                
                $authenticator = new Password_Authenticator($password_post);
                $authenticator->set_authenticated(false);
                
            }
        }
        
    }
    
    /**
     * Checks the password given against the stored password.  If there is an
     * authenticated session, authentication evaluates to true.
     * 
     * @param string    $gps_section_password   The password entered by the user
     * @param WP_Post   $password_post          The password custom post type that 
     *                                          contains the correct password
     * @return boolean|null  true when password given matches the stored password or 
     *                  if there is an authenticated session, otherwise false
     * @since 0.2.1
     */
    private static function authenticate_password( $gps_section_password, $password_post ){
        
        if ( isset( $gps_section_password ) ) {
                        
            // set the authentication session variables via the authenticator
            $authenticator = new Password_Authenticator( $password_post );
            return $authenticator->set_authenticated( $gps_section_password );
                    
        // otherwise, if there is a session variable that says this password section is unlocked...
        } else if ( isset( $_SESSION['gps_password_' . $password_post->ID . '_authenticated'] ) ) {
            return true;
        }
        
    }
    
    /**
     * If the ajax attribute is not present, remove the ajax handler
     * 
     * @param array $attributes the set of shortcode attributes
     */
    private static function handle_ajax_attribute( $attributes ){
        
        if ( ! $attributes['ajax'] ){
            wp_dequeue_script( 'gps-password-ajax-handler' );
        }
        
    }
    
    /**
     * Get the password entered in the form.  If nonce verification fails,
     * return null
     * 
     * @param   WP_Post   $password_post  The Password post type we are authenticating against
     * @return  string   The password entered, or null if nonce verification fails
     * @since 0.2.1
     */
    private static function get_password_entered( $password_post ){
        
        /*
         * Make sure password entered was for this password object.
         * Handles case where different password shortcodes are on one page.
         * To do this, handle the nonce.  If it returns false, assume
         * the password in the form submission is irrelevant for this password
         * shortcode, and ignore it.
         */
        if ( ! wp_verify_nonce( filter_input( INPUT_POST, '_wpnonce' ), 
                'unlock_protected_section_' . $password_post->ID )){
            
            return null;
            
        } else {
            
            return filter_input(INPUT_POST, 'gps-section-password' );
            
        }
        
    }
        
}