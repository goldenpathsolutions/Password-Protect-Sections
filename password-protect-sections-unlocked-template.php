<?php

/**
 * Password Protect Sections UNLOCKED Template
 *  
 * This file contains markup that wraps the content enclosed by the gps_password
 * shortcode when the content has been unlocked.  It allows a visitor to relock
 * the content, and includes styling to help indicate which content was protected.
 * 
 * When the protected section is relocked, the contents of the gps_password shortcode
 * will be governed by the password-protect-sections-locked-template.php file.
 * 
 * @author Patrick Jackson <pjackson@goldenpathsolutions.com>
 * @copyright (c) 2014, Golden Path Solutions, Inc.
 * @version 1.0.2
 * @since 0.1.0
 * 
 */

if ( get_the_ID() ){
    $protected_post_id = get_the_ID();
}

if ( isset($attributes['reload_page']) ){
    $is_reload_page = $attributes['reload_page'];
}

?>
<div class="password-protected-section unlocked">
    <form name="password-protected-section-<?php echo $password_post->ID; ?>" 
          id="password-protected-section_<?php echo $password_post->ID; ?>" 
          action="" method="post" class="password-protected-section">
        
        <?php wp_nonce_field( 'relock_protected_section_'.$password_post->ID ); ?>
        
        <input type="hidden" name="password-name" value="<?php echo $password_post->post_title; ?>"/>
        
        <input type='hidden' name='relock-protected-section' id='relock-protected-section' value='1'/>
        
        <input type="hidden" name="protected-post-id" value="<?php echo $protected_post_id; ?>"/>
        
        <?php if ($is_reload_page){ ?>
            <input type="hidden" name="is-reload-page" value="1"/>
        <?php } ?>
        
        <a class='relock-link' href="#" onclick="parentNode.submit()" title="click to relock content"><i class="fa fa-unlock"></i></a>
        
    </form>
    <?php echo trim($content); ?>
</div>