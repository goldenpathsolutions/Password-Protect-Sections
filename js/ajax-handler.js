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

// preload ajax gif
var $loading_image = jQuery('<img class="password-ajax-loading" width="15"'
        + 'height="15" src="' + gps_ajax_data.ajax_loader_url +'">');

/**
 * Main function for handling ajax behaviors
 * 
 * @param {object} $ The jQuery object
 */
jQuery(document).ready(function($) {
    
    var $section, $submit_button, $relock_link, $password_label;
        
    // Don't know whether we're locked or unlocked to start, so initialize both
    init_submit_button();
    init_relock();
    
    
    /**
     * add listeners to submit button and override non-ajax behaviors
     * 
     * @since 1.0.0
     */
    function init_submit_button(){
        
        $section = $("div.password-protected-section");
        $submit_button = $("form.password-protected-section button[type='submit']"),
        $password_label = $section.find("label");
        
        // add listener to the form submission button
        $submit_button.click( submit_password );
    }
    
    
    /**
     * add listeners to relock link and override non-ajax behaviors
     * 
     * @since 1.0.0
     */
    function init_relock(){
        
        $section = $("div.password-protected-section");
        $relock_link = $("form.password-protected-section a.relock-link");
        
        // remove onclick behavior from relock link
        $relock_link.attr('onclick',null).off('click');

        // add listener to the relock link
        $relock_link.click( handle_relock );
    }
    
    
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
        

        // submit the data via AJAX call
        jQuery.post(gps_ajax_data.ajax_url, data, function(response ) {
                handle_password_response( response );
        }, 'json');
    }
    
    
    /**
     * Handle Response after someone submits a password
     * 
     * @param {array} response
     */
    function handle_password_response( response ){
        
        
        // If the content is empty, try refreshing the page.  There are some
        // cases where the shortcode content is inaccessible as when the
        // shortcode lives in a widget or template.
        if ( response.content && response.content.length === 0 ){
            window.location.reload();
            return;
        }
        
        
        // remove loading image
        $loading_image.detach();
        $password_label.css("padding-left","0");

        // if there was an error, show it with validation error markup
        if (response.error){
            
            var $password_input = $section.find("input[name='gps-section-password']");
            
            $password_label.before( "<p class='gps-error'>" + response.error + "</p>" );
            
            $password_label.addClass("gps-error");
            $password_input.addClass("gps-error");
            
        } else if (response.content){
            // if content is returned, show it
            
            // if the content is not empty, replace the section with it
            $section.replaceWith( response.content );
            
            // remember to add listeners and override non-ajax behaviors to relock
            // link...
            init_relock();
            
        } else {
            // otherwise, reload the page.  This will be the case when the
            // Reload Page option is selected
            window.location.reload();
            return;
        }  
    }
    
    /**
     * Handle relocking form via AJAX
     * 
     * @param {Event} e The click event
     * @since 1.0.0
     */
    function handle_relock(e){
        
        $section.find("a.relock-link i").before($loading_image);
        
        // don't navigate anywhere on clicking the link
        e.preventDefault();
        
        var $this = $(this),
            data = {
                'action': 'get_password_form',
            };
            
        /*
         * add the fields from this instance of the form to the data posted 
         * (a page may have more than one)
         */
        get_form_data( $this.parent("form"), data);

        // We can also pass the url value separately from ajaxurl for front end 
        // AJAX implementations
        jQuery.post(gps_ajax_data.ajax_url, data, function(response) {
                handle_relock_response( response );
        }, 'json');
    }
    
    /**
     * Handle Relock Response
     * 
     * @param {array} response
     */
    function handle_relock_response( response ){
        
        // remove loading image
        $loading_image.detach();
        
        // if an error message was sent, display it; otherwise, replace the
        // protected section with the password form content.
        if (response.error){
            
            var $relock_link = $section.find("a.relock-link");
            
            $relock_link.after( "<p class='gps-error'>" + response.error + "</p>" );
            
            $relock_link.addClass("gps-error");
            
        } else if (response.content){
            
            $section.replaceWith( response.content );
            
            // remember to add listeners and override non-ajax behaviors to 
            // password form...
            init_submit_button();
            
        } 
    }
    
    /**
     * Get Form Data
     * 
     * Iterate through all of the form input fields, and add their values to the
     * data array using their names as keys.
     * 
     * @param {object} $form   The jQuery form element
     * @param {array} data    The data to be posted via ajax
     * @since 1.0.0
     */
    function get_form_data( $form, data ){
        $form.children("input").each( function(){
            var $this = $(this);
            data[$this.attr("name")] = $this.val();
        });
    }
    
    
});