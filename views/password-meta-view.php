<?php

/* 
 * Password Meta View
 * 
 * This contains the information for rendering the view for the 
 * Password Post Type meta box where the custom fields are entered.
 * 
 * Note that WP_Post object is available as $post
 * 
 * Author: Patrick Jackson, Golden Path Solutions
 * Author URL: http://www.goldenpathsolutions.com
 * Created: 2014-06-25
 * 
 */

    //get values if they exist
    $value = get_post_meta( $post->ID, '_gps_password', true);
    $stored_message = get_post_meta( $post->ID, '_gps_password_failed_message', true);
    
    //message used when wrong password is entered
    $failed_message = $stored_message ? $stored_message : "The password you entered did not match the one on record for this section";

?>

<div class="field-section">
    <h4>Password</h4>
    <p class="field-description">Enter the Password used to unlock protected content</p>
    <input type='text' name='password_input' id='password_input' value='<?php echo $value ?>'/>
</div>
<hr/>
<div class="field-section">
    <h4>Failed Password Message</h4>
    <p class="field-description">Enter the message used if a visitor enters the wrong password</p>
    <input type='text' name='password_failed_message' id='password_failed_message' size="80" maxlength="1024"value='<?php echo $failed_message ?>'/>
</div>