<?php
/** 
 * Password Shortcode Handler
 * 
 * This class manages the shortcode used by this plugin
 * 
 * <code>[gps-password title='<title of password>'] Protected Content [/gps-password]</code>
 * 
 * @author Patrick Jackson <pjackson@goldenpathsolutions.com>
 * @copyright (c) 2014, Golden Path Solutions, Inc.
 * @link http://www.goldenpathsolutions.com
 * @version 1.0.1
 * @since 0.1.3
 *
 * @package password-protect-sections
 * 
 */
class Password_Shortcode_Handler {
        
    function __construct() {
        
        // register the style
        add_action('wp_enqueue_scripts', array('Password_Shortcode_Handler', 'enqueue_style'));
        
        // Add the shortcode
        add_shortcode( 'gps-password', array( 'Password_Shortcode_Handler', 'gps_password_shortcode') );
    }
    
    public static function enqueue_style(){
        wp_enqueue_style( 'gps_password_style', plugins_url('password-protect-sections/css/style.css'));
    }
    
    /**
     * Starting point for all the work done by this shortcode
     * @param type $attributes
     */
    public static function gps_password_shortcode( $attributes_in, $content = null ){
        
        $unlocked = false;
        $password_entered = false;
        
        $atts = shortcode_atts( array(
            'title' => null
        ), $attributes_in );

        $password_post = get_page_by_title( $atts['title'], null, 'gps_password' );
        
        //if we can't find a password, then don't try to protect the content
        if ( ! isset( $password_post ) )  return do_shortcode($content);
        
        //check to see if we're relocking
        $relock_protected_section = filter_input( INPUT_POST, 'relock_protected_section' );
        if ( $relock_protected_section ){
            
            //verify the nonce
            if ( wp_verify_nonce( filter_input(INPUT_POST, '_wpnonce'), 
                    'relock_protected_section_'.$password_post->ID )){
                
                unset( $_SESSION['gps_password_' . $password_post->ID . 
                    '_authenticated'] );
            }
        }
                        
        //check to see if we're handling a password submission
        $gps_section_password = filter_input(INPUT_POST, 'gps_section_password' );
        
        /*
         * Make sure password entered was for this password object.
         * Handles case where different password shortcodes are on one page.
         * To do this, handle the nonce.  If it returns false, assume
         * the password in the form submission is irrelevant for this password
         * shortcode, and ignore it.
         */
        if ( !wp_verify_nonce( filter_input( INPUT_POST, '_wpnonce' ), 'unlock_protected_section_'.$password_post->ID )){
            $gps_section_password = null;
        }
            
        if ( isset( $gps_section_password ) ) {
            
            $password_entered = true;
            $unlocked = self::handle_password_submission( $password_post, $gps_section_password );
        
        } else if ( isset( $_SESSION['gps_password_' . $password_post->ID . '_authenticated'] ) ) {
            $unlocked = true;
        }
        
        $hide_content = !$unlocked; //don't hide content if unlocked
       
        return $hide_content ? self::get_replacement_content( $password_post, 
                $content, $password_entered && !$unlocked, $unlocked ) 
                : do_shortcode( self::get_replacement_content( $password_post, 
                        $content, false, $unlocked) );
          
    }
    
    private static function get_replacement_content( $password_post, $content = null, 
            $password_failed, $unlocked ){
        
        ob_start();
        
        if ( $unlocked ){ 
            $template_file = self::find_template_file( "/password-protect-sections-unlocked-template.php" );
        } else {
            $template_file = self::find_template_file ( "/password-protect-sections-locked-template.php" );
        }
        
        require( $template_file );
        
        return ob_get_clean();
        
    }
    
    private static function find_template_file( $default_template_file_name ){        
        
        //first, check the child theme first.  
        //If no child, it'll just return the active theme
        $template_file = get_template_directory() . $default_template_file_name;
        if ( file_exists($template_file) ){
            return $template_file;
        }
        
        //second, try the parent theme.
        //if no parent, this shouldn't be reached, but will return active theme in any case
        $template_file = get_template_directory() . $default_template_file_name;
        if (file_exists($template_file)){
            return $template_file;
        }
        
        //finally choose plugin default
        return dirname( __FILE__ ) . '/..' . $default_template_file_name;
        
    }
    
    private static function handle_password_submission( $password_post, $password ){
        
        
        $stored_password = get_post_meta( $password_post->ID, '_gps_password', true);
        
        $authenticated = trim($password) === trim($stored_password);
        
        if ( $authenticated ){
            $_SESSION['gps_password_' . $password_post->ID . '_authenticated'] = true;
        }
        
        return $authenticated;
            
    }
}