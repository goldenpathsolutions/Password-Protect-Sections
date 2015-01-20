<?php

/* 
 * This file contains the content inserted into the content, replacing the
 * content enclosed by the gps_password shortcode tags while that content 
 * is password protected.
 * 
 * Once that code is unlocked by entering the password, the contents of this 
 * file will be replaced by the originally enclosed content.
 * 
 */

global $password_failed;

    $failed_message = get_post_meta( $password_post->ID, '_gps_password_failed_message', true);

?>

<div class="password-protected-section locked">
    <div class="lock-icon" title="Enter password to unlock content."><i class="fa fa-lock"></i></div>
    
    <?php if ($password_failed) { 
        echo "<p class='gps-error'>" . $failed_message . "</p>";
    } ?>
        
    <?php echo $password_post->post_content; ?>
    
<form name="password_protected_section_<?php echo $password_post->ID; ?>" id="password_protected_section_<?php echo $password_post->ID; ?>" action="" method="post">
    
    <?php wp_nonce_field( 'unlock_protected_section_'.$password_post->ID ); ?>
    <label <?php echo $password_failed ? "class='gps-error'" : ""; ?>>Password</label>
    <input type="password" name="gps_section_password" id="gps_section_password_<?php echo $password_post->ID ?>" size="15" <?php echo $password_failed ? "class='gps-error'" : ""; ?> />
    <button type="submit">Unlock</button>
    
</form>
</div>
