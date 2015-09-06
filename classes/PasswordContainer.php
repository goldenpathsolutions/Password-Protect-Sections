<?php
namespace gps\password_protect_section;

/**
 * Password Container
 * 
 * Holds the content protected by passwords.  Also keeps track of which password
 * field contains a piece of content.  This is used by ajax when injecting
 * protected content.
 * 
 * @author Patrick Jackson <pjackson@goldenpathsolutions.com>
 * @copyright (c) 2015, Golden Path Solutions, Inc.
 * @version 1.0.1
 * @since 0.3.0
 * 
 * @package password-protect-sections
 * 
 */

namespace gps\password_protect_sections;


/**
 * @since 0.3.0
 */
class PasswordContainer {
    
    /**
     *
     * @var array   3d array maps password instances to their protected content 
     *      blocks. Dimensions include the following.
     *      int     unique identifier for password object
     *      int     unique identifier for password instance
     *      string  content protected by that instance
     * @since   0.3.0
     */
    private $protected_sections = array();
    
    
    
}
