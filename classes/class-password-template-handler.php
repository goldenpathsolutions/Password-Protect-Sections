<?php

/** 
 * Password Template Handler
 * 
 * Manages which template to use.
 * 
 * @author Patrick Jackson <pjackson@goldenpathsolutions.com>
 * @copyright (c) 2015, Golden Path Solutions, Inc.
 * @link http://www.goldenpathsolutions.com
 * @version 1.0.0
 * @since 0.2.0
 *
 * @package password-protect-sections
 * 
 */

class Password_Template_Handler{
    
    
    /**
     * Find Template File
     * 
     * Governs where templates can live.  
     * 
     * @param   string  $default_template_file_name The name of the default template 
     *                                              file.
     * @return  string  The absolute path to the template file
     */
    public static function find_template_file( $default_template_file_name ){        
        
        /*
         * first, check the child theme.
         * If no child, it'll just return the active theme
         */
        $template_file = get_template_directory() . $default_template_file_name;
        if ( file_exists($template_file) ){
            return $template_file;
        }
        
        /*
         * second, try the parent theme.
         * if no parent, this shouldn't be reached, but will return active theme in any case
         */
        $template_file = get_template_directory() . $default_template_file_name;
        if (file_exists($template_file)){
            return $template_file;
        }
        
        // finally, choose plugin default
        return dirname( __FILE__ ) . '/..' . $default_template_file_name;
    }
}