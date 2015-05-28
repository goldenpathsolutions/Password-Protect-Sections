<?php

/**
 * Shortcode Parser 
 * 
 * This is not a fully functional parser.  We're just pulling the contents between
 * start and end tag of a given shortcode.
 * 
 * Maybe we can add to it. :)
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

class Shortcode_parser{
    
    /**
     * An array of starting shortcodes along with their position.
     * Watch for self-closing shortcodes.
     * 
     * @var {array}
     */
    var $shortcode_matches;
    
    /**
     * An array of ending shortcodes along with their positions.
     * 
     * @var type 
     */
    var $closing_shortcodes;
    
    var $shortcode_name;
    
    
    /**
     * 
     * This constructor accepts the input to be parsed.
     * Populates the $opening_shortcodes and $closing_shortcodes
     * 
     * @param {string} $content input to be parsed
     * @param {string} $shortcode_name unique name of shortcode
     */
    function __construct( $content, $shortcode_name ){
        
        $this->shortcode_name = $shortcode_name;
        
     
        // shortcode regex pattern (taken from WP Core function get_shortcode_regex()
        $pattern = '/(.?)\[('.$this->shortcode_name.')\b(.*?)(?:(\/))?\](?:(.+?)\[\/\2\])?(.?)/s';
        
        // populate the shortcode matches
        preg_match_all( $pattern, $content, $this->shortcode_matches );
        
    }
    
    
    /**
     * Get Shortcode Content
     * 
     * Returns an array whose elements are the contents of each shortcode found.  
     * No content is returned for self-closing shortcodes.
     * 
     * @return {array} content for given shortcode(s)
     */
    public function get_shortcode_content(){
        
        $full_pattern_matches = $this->shortcode_matches[0];
        
        // regex to find the opening tag
        $opening_pattern = '/\[('.$this->shortcode_name.')\b(.*?)(?:(\/))?\]/s';
        
        // regex to find last closing tag
        $closing_pattern = '/\[\/('.$this->shortcode_name.')](?!.*\[\/('.$this->shortcode_name.')])/s';
        
        /*
         * populate the shortcode matches
         */
        $content = array();
        
        foreach($full_pattern_matches as $subject){
                        
            // remove the first, opening shortcode
            $subject = preg_replace( $opening_pattern, '', $subject, 1 );
            
            // remove the last, closing shortcode
            $subject = preg_replace( $closing_pattern, '', $subject );
            
            array_push($content, $subject);
            
            
        }
        
        return $content;
        
    }
    
    
}
