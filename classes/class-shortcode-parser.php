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

/**
 * @since 0.2.0
 */
class Shortcode_parser{
    
    /**
     * An array of starting shortcodes along with their position.
     * Watch for self-closing shortcodes.
     * 
     * @var array
     */
    var $shortcode_matches;
    
    /**
     * An array of ending shortcodes along with their positions.
     * 
     * @var array
     */
    var $closing_shortcodes;
    
    /**
     * Holds the shortcode name used in the parser shortcode pattern
     * 
     * @var string name of the shortcode being parsed
     */
    var $shortcode_name;
    
    /**
     * Shortcode title is used to filter the results of the pattern results
     * 
     * @var string  title of the specific shortcode instance 
     */
    var $shortcode_title;
    
    
    /**
     * 
     * This constructor accepts the input to be parsed.
     * Populates the $opening_shortcodes and $closing_shortcodes
     * 
     * @param string $content           input to be parsed
     * @param string $shortcode_name    unique name of shortcode
     * @param string $password_title    title of password object
     * 
     * @since 0.2.0
     */
    public function __construct( $content, $shortcode_name, $shortcode_title ){
        
        $this->shortcode_name = $shortcode_name;
        
        $this->shortcode_title = $shortcode_title;
        
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
     * @return array content for given shortcode(s)
     * 
     * @since 0.2.0
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
        
        foreach( $full_pattern_matches as $subject ){
            
            /*
             * Skip this shortcode if it doesn't have the right title.
             * This happens if there are two different password shortcodes in
             * the same content block.
             */
            if ( false === $this->skip_block( $subject, $opening_pattern, $this->shortcode_title ) ){
                continue;
            }
                        
            // remove the first, opening shortcode
            $subject = preg_replace( $opening_pattern, '', $subject, 1 );
            
            // remove the last, closing shortcode
            $subject = preg_replace( $closing_pattern, '', $subject );
            
            array_push($content, $subject);
            
        }
        
        return $content;
        
    }
    
    /**
     * Used by get_shortcode_content
     * 
     * @param type $subject
     * @param type $opening_pattern
     * @param type $shortcode_title
     * @return type
     */
    private function skip_block( $subject, $opening_pattern, $shortcode_title ){
        
        $matches = array();
        
        preg_match( $opening_pattern, $subject, $matches );
        
        return strpos( $matches[0], $shortcode_title );
        
    }
    
}
