<?php

/** 
 * Password Authenticator
 * 
 * Handles authenticating a password submission and returning the protected
 * content
 * 
 * @author Patrick Jackson <pjackson@goldenpathsolutions.com>
 * @copyright (c) 2015, Golden Path Solutions, Inc.
 * @link http://www.goldenpathsolutions.com
 * @version 1.0.0
 * @since 0.2.0
 *
 * @package password-protect-sections
 */

class Password_Authenticator{
    
    var $password_post;
    
    var $is_authenticated = false;
    
    var $error;
    
    /**
     * 
     * @param {object} $password_post   The Password custom post type to authenticate
     *                                  against
     * @since 1.0.0
     */
    function __construct($password_post) {
        $this->password_post = $password_post;
    }
    
    
    /**
     * 
     * Is Authenticated
     * 
     * Determine whether a password entered matches the password stored for
     * this gps_password object.  Sets $error with error message on failure.
     * Also sets session variables for authenticatd or failed authentication.
     * 
     * @param string $password              The password entered that is being tested
     * @return boolean  True if authenticated, otherwise false
     * 
     * @since 1.0.0
     */
    public function is_authenticated( $password ){
        
        $stored_password = get_post_meta( $this->password_post->ID, '_gps_password', true);
        
        $authenticated = trim($password) === trim($stored_password);
        
        if ( $authenticated ){
            $_SESSION['gps_password_' . $this->password_post->ID 
                    . '_authenticated'] = true;
            $_SESSION['gps_password_' . $this->password_post->ID 
                    . '_failed'] = false;
        } else {
            $_SESSION['gps_password_' . $this->password_post->ID 
                    . '_failed'] = true;
            
            $this->error = get_post_meta( $this->password_post->ID, 
                    '_gps_password_failed_message', true);
        }
        
        return $authenticated;
    }
    
}
