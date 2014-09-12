<?php
/* 
 * Password Shortcode Handler
 * 
 * This class manages the shortcode used by this plugin
 * 
 * [gps-password title='<title of password>'] Protected Content [/gps-password]
 */
class Password_Shortcode_Handler {
        
    function __construct() {
        
        //register the style
        add_action('wp_enqueue_scripts', array(&$this, 'enqueue_style'));
        
        //Add the shortcode
        add_shortcode( 'gps-password', array( &$this, 'gps_password_shortcode') );
    }
    
    public function enqueue_style(){
        wp_enqueue_style( 'gps_password_style', plugins_url('password-protect-sections/css/style.css'));
    }
    
    /**
     * Starting point for all the work done by this shortcode
     * @param type $attributes
     */
    public function gps_password_shortcode( $attributes_in, $content = null ){
        
        $unlocked = false;
        $password_entered = false;
        
        $atts = shortcode_atts( array(
            'title' => null
        ), $attributes_in );

        $password_post = get_page_by_title( $atts['title'], null, 'gps_password' );
        
        //if we can't find a password, then don't try to protect the content
        if ( !$password_post )  return do_shortcode($content);
        
        //first, check to see if we're relocking
        if ( isset( $_POST['relock_protected_section'] ) ){
            
            //verify the nonce
            if ( isset( $_POST['_wpnonce'] ) && 
                    wp_verify_nonce( $_POST['_wpnonce'], 
                            'relock_protected_section_'.$password_post->ID )){
                unset( $_SESSION['gps_password_' . $password_post->ID . 
                    '_authenticated'] );
            }
            
        }
        
        
        //check to see is we're handling a password submission
        if ( isset( $_POST['gps_section_password'] ) ){
            
            $password_entered = true;
            $unlocked = $this->handle_password_submission( $password_post, 
                    $_POST['gps_section_password'] );
        
            
        } else if ( isset( $_SESSION['gps_password_' . $password_post->ID . '_authenticated'] ) )
                $unlocked = true;
        
        $hide_content = !$unlocked; //don't hide content if unlocked
       
        return $hide_content ? $this->get_replacement_content( $password_post, 
                $content, $password_entered && !$unlocked, $unlocked ) 
                : do_shortcode( $this->get_replacement_content($password_post, 
                        $content, false, $unlocked) );
          
    }
    
    private function get_replacement_content( $password_post, $content = null, 
            $password_failed, $unlocked ){
        
        ob_start();
        
        if ( $unlocked){ 
            $template_file = $this->find_template_file( "/password-protect-sections-unlocked-template.php" );
        } else {
            $template_file = $this->find_template_file ( "/password-protect-sections-locked-template.php" );
        }
        
        require_once( $template_file );
        
        return ob_get_clean();
        
    }
    
    private function find_template_file( $default_template_file_name ){        
        
        //first, check the child theme first.  
        //If no child, it'll just return the active theme
        $template_file = get_template_directory() . $default_template_file_name;
        if ( file_exists($template_file) )
            return $template_file;
        
        //second, try the parent theme.
        //if no parent, this shouldn't be reached, but will return active theme in any case
        $template_file = get_template_directory() . $default_template_file_name;
        if (file_exists($template_file))
            return $template_file;
        
        //finally choose plugin default
        return dirname( __FILE__ ) . '/..' . $default_template_file_name;
        
    }
    
    private function handle_password_submission( $password_post, $password ){
        
        // verify this came from a real user and not a hacker
        if ( !wp_verify_nonce( $_POST['_wpnonce'], 'unlock_protected_section_'.$password_post->ID ))
            return $password_post->ID;
        
        $stored_password = get_post_meta( $password_post->ID, '_gps_password', true);
        
        $authenticated = trim($password) === trim($stored_password);
        
        if ( $authenticated )
            $_SESSION['gps_password_' . $password_post->ID . '_authenticated'] = true;
        
        return $authenticated;
            
    }
}