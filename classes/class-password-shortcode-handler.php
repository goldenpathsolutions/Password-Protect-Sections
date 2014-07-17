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
        
        
        //check to see is we're handling a password submission
        if ( $_POST['gps_section_password'] ){
            $password_entered = true;
            $unlocked = $this->handle_password_submission( $password_post, $_POST['gps_section_password'] );
        } else if ( $_SESSION['gps_password_' . $password_post->ID . '_authenticated'] )
                $unlocked = true;
        
        $hide_content = !$unlocked; //don't hide content if unlocked
       
        return $hide_content ? $this->get_replacement_content( $password_post, $content, $password_entered && !$unlocked ) : do_shortcode($content);
          
    }
    
    private function get_replacement_content( $password_post, $content = null, $password_failed ){
        
        ob_start();
        
        require_once( dirname( __FILE__ ) . '/../views/password_protected_content_view.php');
        
        return ob_get_clean();
        
    }   
    
    private function handle_password_submission( $password_post, $password ){
        
        $stored_password = get_post_meta( $password_post->ID, '_gps_password', true);
        
        $authenticated = trim($password) === trim($stored_password);
        
        if ( $authenticated )
            $_SESSION['gps_password_' . $password_post->ID . '_authenticated'] = true;
        
        return $authenticated;
            
    }
}