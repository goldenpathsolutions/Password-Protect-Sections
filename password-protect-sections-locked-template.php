<?php

/**
 * Password Protect Sections LOCKED Template
 *
 * This file contains content that replaces the protected content wrapped by the
 * gps_password shortcode while the protected section is in a locked state.
 *
 * It contains a password entry form as well as markup and styling indicating
 * the protected section.
 *
 * @author Patrick Jackson <pjackson@goldenpathsolutions.com>
 * @copyright (c) 2014, Golden Path Solutions, Inc.
 * @version 1.0.2
 * @since 0.1.0
 *
 * @package password-protect-sections
 */

$failed_message = get_post_meta( $password_post->ID, '_gps_password_failed_message', true );

// handle ajax case where $password_failed is null on loading content
if ( ! isset( $password_failed ) ) {
	$password_failed = false;
}


if ( get_the_ID() ) {
	$protected_post_id = get_the_ID();
}

if ( isset( $attributes['reload_page'] ) ) {
	$is_reload_page = $attributes['reload_page'];
}

?>

<div class="password-protected-section locked">
	<div class="lock-icon" title="Enter password to unlock content."><i class="fa fa-lock"></i></div>

	<?php if ( $password_failed ) {
		echo "<p class='gps-error'>" . $failed_message . "</p>";
	} ?>

	<?php echo trim( wpautop( $password_post->post_content ) ) ?>

	<form name="password-protected-section-<?php echo $password_post->ID; ?>"
	      id="password-protected-section_<?php echo $password_post->ID; ?>"
	      action="" method="post" class="password-protected-section">

		<?php wp_nonce_field( 'unlock_protected_section_' . $password_post->ID ); ?>

		<input type="hidden" name="password-name"
		       value="<?php echo $password_post->post_title; ?>"/>

		<input type="hidden" name="password-instance"
		       value="<?php echo $password_instance_idx; ?>"/>

		<input type="hidden" name="protected-post-id"
		       value="<?php echo $protected_post_id; ?>"/>

		<?php if ( $is_reload_page ) { ?>
			<input type="hidden" name="is-reload-page" value="1"/>
		<?php } ?>

		<label for="gps-section-password-<?php echo $password_post->ID ?>"
			<?php echo $password_failed ? "class='gps-error'" : ""; ?>>Password</label>

		<input type="password" name="gps-section-password"
		       id="gps-section-password-<?php echo $password_post->ID ?>"
		       size="15" <?php echo $password_failed ? "class='gps-error'" : ""; ?> />

		<button type="submit">Unlock</button>

	</form>
</div>
