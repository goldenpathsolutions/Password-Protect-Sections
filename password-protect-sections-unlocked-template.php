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


?>

<div class="password-protected-section unlocked">
    <form name="password_protected_section_<?php echo $password_post->ID; ?>" id="password_protected_section_<?php echo $password_post->ID; ?>"action="" method="post">
        <?php wp_nonce_field( 'relock_protected_section_'.$password_post->ID ); ?>
        <input type='hidden' name='relock_protected_section' id='relock-protected-section' value='1'/>
        <a class='relock-link' href="#" onclick="parentNode.submit()" title="click to relock content"><i class="fa fa-unlock"></i></a>
    </form>
        
    <?php echo $content; ?>
    

</div>