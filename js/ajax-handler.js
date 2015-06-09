/* global gps_ajax_data */
/* global jQuery */

/** 
 * AJAX Handler for Password Protect Sections plugin
 * 
 * This JavaScript replaces the login form with protected content when the correct
 * password is entered, and vice versa when the "lock" icon is clicked.
 * 
 * @author Patrick Jackson <pjackson@goldenpathsolutions.com>
 * @version 1.0.0
 * @since 0.2.0
 * 
 * 
 * 
 */

var $content_store = [];
var $locked_section;

// preload ajax gif
var $loading_image = jQuery('<img class="password-ajax-loading" width="15"'
        + 'height="15" src="' + gps_ajax_data.ajax_loader_url +'">');

/**
 * Main function for handling ajax behaviors
 * 
 * @param {object} $ The jQuery object
 */
jQuery(document).ready(function($) {
    
    var $submit_button = $("form.password-protected-section button[type='submit']"),
        $relock_link = $("form.password-protected-section a.relock-link");
    
    // add listener to the form submission button
    $submit_button.click( submit_password );
    
    // add listener to the relock link
    $relock_link.click( handle_relock )
    
    
    /**
     * handles submitting form and response via AJAX
     * 
     * @param {Event} e The click event
     * @since 1.0.0
     */
    function submit_password(e){
        
        $submit_button.after($loading_image);
        $password_label.css("padding-left","26px");
        
                
        var $this = $(this),
            data = {
                'action': 'get_password_protected_content',
            };
        
        // Don't let the button submit the form.  Do it here.
        e.preventDefault();
        
        /*
         * add the fields from this instance of the form to the data posted 
         * (a page may have more than one)
         */
        get_form_data( $this.parent("form"), data);
        //console.log(data); //DEBUG
        

        // submit the data via AJAX call
        jQuery.post(gps_ajax_data.ajax_url, data, function(response, status, jqXHR) {
                handle_password_response( response, status, jqXHR);
        }, 'json');
    }
    
    /**
     * handles relocking form via AJAX
     * 
     * @since 1.0.0
     */
    function handle_relock(){
        var $this = $(this),
            data = {
                'action': 'relock_content',
            };
        
        // Don't let the link submit the form.  Do it here.
        e.preventDefault();

        // We can also pass the url value separately from ajaxurl for front end AJAX implementations
        jQuery.post(gps_ajax_data.ajax_url, data, function(response) {
                handle_password_response( response );
        });
        // remove loading image
        $loading_image.detach();
    }
    
    /**
     * Get Form Data
     * 
     * Iterate through all of the form input fields, and add their values to the
     * data array using their names as keys.
     * 
     * @param jQueryElement $form   The jQuery form element
     * @param array data    The data to be posted via ajax
     * @since 1.0.0
     */
    function get_form_data( $form, data ){
        $form.children("input").each( function(){
            var $this = $(this);
            data[$this.attr("name")] = $this.val();
        });
    }
    
    /**
     * Handle Password Response
     * 
     * @param {type} response
     */
    function handle_password_response( response ){
        
        var $section = $("div.password-protected-section");
        
        if (response["error"]){
            
            var $password_label = $section.find("label");
            var $password_input = $section.find("input[name='gps-section-password']");
            
            $password_label.before( "<p class='gps-error'>" + response["error"] + "</p>" );
            
            $password_label.addClass("gps-error");
            $password_input.addClass("gps-error");
            
        } else if (response["content"]){
            
            $locked_section = $section.replaceWith( response["content"] );
            
        }        
    }
});