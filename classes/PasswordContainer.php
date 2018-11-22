<?php

namespace gps\password_protect_section;

/**
 * Password Container
 *
 * Holds the content protected by passwords.  Also keeps track of which password
 * field contains a piece of content.  This is used by ajax when injecting
 * protected content.
 *
 * @author Patrick Jackson <pjackson@goldenpathsolutions.com>
 * @copyright (c) 2015, Golden Path Solutions, Inc.
 * @version 1.0.1
 * @since 0.3.0
 *
 * @package password-protect-sections
 *
 */

namespace gps\password_protect_sections;


/**
 * @since 0.3.0
 */
class PasswordContainer {

	/**
	 *
	 * @var array   2D array maps password instances to their protected content
	 *      blocks. Dimensions include the following.
	 * @type int     unique identifier for password object (first key)
	 * @type int     unique identifier for password instance (second key)
	 * @type string  content protected by that instance (value)
	 * @since   0.3.0
	 */
	private $protected_sections = array();

	/**
	 *
	 * @param \WP_POST $password_post The password object whose instance content
	 *                                  is being stored
	 * @param string $content The content for the password instance
	 *                                  being stored
	 *
	 * @return int      The index of the password instance, which will serve as
	 *                  a unique identifier (when combined with the password
	 *                  object's ID)
	 */
	public function add( $password_post, $content = null ) {

		// push content onto end of $password_post's array
		$this->protected_sections[ $password_post->ID ][] = $content;

		// return the assigned index as the password instance key
		return sizeof( $this->protected_sections[ $password_post->ID ] ) - 1;

	}

}
