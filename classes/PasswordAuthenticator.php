<?php

/**
 * Password Authenticator
 *
 * Handles authenticating a password submission and returning the protected
 * content
 *
 * @author Patrick Jackson <pjackson@goldenpathsolutions.com>
 * @copyright (c) 2015, Golden Path Solutions, Inc.
 * @link http://www.goldenpathsolutions.com
 * @version 1.0.1
 * @since 0.2.0
 *
 * @package password-protect-sections
 */

namespace gps\password_protect_sections;

class Password_Authenticator {

	/**
	 * The Password custom post type being authenticated against
	 *
	 * @access private
	 * @var object
	 * @since 0.2.0
	 */
	private $password_post;

	/**
	 * The state of the last attempt to authenticate: true if passwords matched,
	 * otherwise false
	 *
	 * @access private
	 * @var boolean
	 * @since 0.2.0
	 */
	private $is_authenticated = false;

	/**
	 * Holds the last error message created during authentication if it exists.
	 *
	 * @access private
	 * @var string
	 * @since 0.2.0
	 */
	private $error;


	/**
	 *
	 * @param WP_Post $password_post The Password custom post type to authenticate
	 *                                  against
	 *
	 * @since 0.2.0
	 */
	public function __construct( $password_post ) {
		$this->password_post = $password_post;
	}

	/**
	 * Return the result of the last attempt to authenticate.  Return false by
	 * default (no attempt to authenticate yet)
	 *
	 * @return boolean True if most recent password given was authenticated,
	 *                 otherwise false.
	 * @since 0.2.0
	 */
	public function is_authenticated() {
		return $this->is_authenticated;
	}

	/**
	 * Use to retrieve the message when there is an error.
	 *
	 * @return string The last error message written. Null if no error.
	 *
	 * @since 0.2.0
	 */
	public function get_error() {
		return $this->error;
	}


	/**
	 *
	 * Set Authenticated
	 *
	 * Determine whether a password entered matches the password stored for
	 * this gps_password object.  Sets $error with error message on failure.
	 * Also sets session variables for authenticatd or failed authentication.
	 *
	 * @param string|boolean $password The password entered that is being tested
	 *                                      If false, set to deauthenticated
	 *
	 * @return boolean  True if authenticated, otherwise false
	 *
	 * @since 0.2.0
	 */
	public function set_authenticated( $password ) {

		$stored_password = get_post_meta( $this->password_post->ID, '_gps_password', true );

		$authenticated = false;

		// if $password is not false, then test $password against $stored_password...
		if ( false !== $password ) {

			$authenticated = trim( $password ) === trim( $stored_password ) &&
			                 null !== $password;

			// if password was correct, set session authentication var to true
			// and make sure any failed password error is unset
			if ( true === $authenticated ) {

				$_SESSION[ 'gps_password_' . $this->password_post->ID
				           . '_authenticated' ] = true;

				unset( $_SESSION[ 'gps_password_' . $this->password_post->ID
				                  . '_failed' ] );

			} else {

				// otherwise, if the password was wrong, make sure any lingering
				// authentication session var is unset, set the password failed
				// session var to true, and pull the error message

				unset( $_SESSION[ 'gps_password_' . $this->password_post->ID
				                  . '_authenticated' ] );

				$this->error = get_post_meta( $this->password_post->ID,
					'_gps_password_failed_message', true );

			}

		} else {

			// otherwise, set authenticated to false explicitly. 
			// expire authentication: remove session variables, and set 
			// $authenticated to false

			unset( $_SESSION[ 'gps_password_' . $this->password_post->ID
			                  . '_authenticated' ] );
			unset( $_SESSION[ 'gps_password_' . $this->password_post->ID
			                  . '_failed' ] );

		}

		return $authenticated;
	}

}
