<?php

/**
 * This class contains the Password custom post type and related functions
 *
 * @author Patrick Jackson <pjackson@goldenpathsolutions.com>
 * @copyright (c) 2015, Golden Path Solutions, Inc.
 * @link http://www.goldenpathsolutions.com
 * @version 1.1.1
 * @since 0.1.0
 *
 * @package password-protect-sections
 */

namespace gps\password_protect_sections;

class Password_Post_Type {

	var $capability = "edit_passwords";

	public function __construct() {

		//Add capability to use this plugin to administrator by default
		add_action( 'admin_init', array( &$this, 'add_capability_to_administrator_role' ) );

		//Add this post type
		add_action( 'init', array( &$this, 'create_post_type' ) );

		//Add styling for admin pages
		add_action( 'admin_enqueue_scripts', array( &$this, 'enqueue_admin_style' ) );

		//Only show admin menu items to those with access
		add_action( 'admin_menu', array( &$this, 'manage_ui_access' ) );

		//Add the custom meta boxes for this post type
		add_action( 'add_meta_boxes', array( &$this, 'add_meta_box' ) );

		//Handle saving meta box inputs
		add_action( 'save_post', array( &$this, 'save_meta_box_data' ) );


	}

	/**
	 *
	 * @param string $password_post_name
	 *
	 * @return WP_Post
	 *
	 * @since 0.1.0
	 */
	public static function get_password_post_by_name( $password_post_name ) {
		return get_page_by_title( $password_post_name, null, 'gps_password' );
	}

	/**
	 *
	 * @param   int $password_post_id
	 *
	 * @return  WP_Post
	 *
	 * @since 0.1.0
	 */
	public static function get_password_post_by_id( $password_post_id ) {
		return get_post( $password_post_id );
	}

	/**
	 * @since 0.1.0
	 */
	public function enqueue_admin_style() {
		wp_enqueue_style( 'gps_password_admin_style', plugins_url( 'css/style-admin.css', dirname(__FILE__) ) );
	}

	/**
	 * Define the Password custom Post Type
	 * @since 0.1.0
	 */
	public function create_post_type() {

		//define the capabilities in the context of this post type
		$capabilities = array(
			'publish_posts'       => 'publish_password',
			'edit_posts'          => 'edit_password',
			'edit_others_posts'   => 'edit_others_passwords',
			'delete_posts'        => 'delete_passwords',
			'delete_others_posts' => 'delete_others_passwords',
			'read_private_posts'  => 'read_private_passwords',
			'edit_post'           => 'edit_password',
			'delete_post'         => 'delete_password',
			'read_post'           => 'read_password',
		);

		$labels = array(
			'name'                  => __( 'Passwords' ),
			'singular_name'         => __( 'Password', 'password-protect-sections' ),
			'not_found'             => __( 'No Passwords Defined', 'password-protect-sections' ),
			'not_found_in_trash'    => __( 'No Passwords Defined in Trash', 'password-protect-sections' ),
			'menu_name'             => __( 'Passwords', 'password-protect-sections' ),
			'name_admin_bar'        => __( 'Password', 'password-protect-sections' ),
			'archives'              => __( 'Password Archives', 'password-protect-sections' ),
			'attributes'            => __( 'Password Attributes', 'password-protect-sections' ),
			'parent_item_colon'     => __( 'Parent Password:', 'password-protect-sections' ),
			'all_items'             => __( 'All Passwords', 'password-protect-sections' ),
			'add_new_item'          => __( 'Add New Password', 'password-protect-sections' ),
			'add_new'               => __( 'Add New', 'password-protect-sections' ),
			'new_item'              => __( 'New Password', 'password-protect-sections' ),
			'edit_item'             => __( 'Edit Password', 'password-protect-sections' ),
			'update_item'           => __( 'Update Password', 'password-protect-sections' ),
			'view_item'             => __( 'View Password', 'password-protect-sections' ),
			'view_items'            => __( 'View Passwords', 'password-protect-sections' ),
			'search_items'          => __( 'Search Password', 'password-protect-sections' ),
			'featured_image'        => __( 'Featured Image', 'password-protect-sections' ),
			'set_featured_image'    => __( 'Set featured image', 'password-protect-sections' ),
			'remove_featured_image' => __( 'Remove featured image', 'password-protect-sections' ),
			'use_featured_image'    => __( 'Use as featured image', 'password-protect-sections' ),
			'insert_into_item'      => __( 'Insert into Password', 'password-protect-sections' ),
			'uploaded_to_this_item' => __( 'Uploaded to this Password', 'password-protect-sections' ),
			'items_list'            => __( 'Passwords list', 'password-protect-sections' ),
			'items_list_navigation' => __( 'Passwords list navigation', 'password-protect-sections' ),
			'filter_items_list'     => __( 'Filter Passwords list', 'password-protect-sections' ),
		);

		register_post_type( 'gps_password', array(
			'label'               => __( 'Password', 'password-protect-sections' ),
			'labels'              => $labels,
			'description'         => __( 'A Password instance applies protection to all sections of content surrounded by that Password\'s shortcode', 'password-protect-sections' ),
			'public'              => false,
			'has_archive'         => false,
			'show_ui'             => true,
			'hierarchical'        => false,
			'capabilities'        => $capabilities,
			'menu_position'       => 85,
			'supports'            => array( 'title', 'editor', 'author' ),
			'menu_icon'           => 'dashicons-lock',
			'show_in_menu'        => true,
			'show_in_admin_bar'   => true,
			'show_in_nav_menus'   => false,
			'can_export'          => true,
			'exclude_from_search' => true,
			'show_in_rest'        => true,
			'publicly_queryable'  => false,
		) );
	}

	/**
	 * Add capability to use this plugin to administrator
	 * @since 0.1.0
	 */
	public function add_capability_to_administrator_role() {
		$role = get_role( 'administrator' );
		$role->add_cap( 'publish_password' );
		$role->add_cap( 'edit_password' );
		$role->add_cap( 'edit_others_passwords' );
		$role->add_cap( 'delete_passwords' );
		$role->add_cap( 'delete_others_passwords' );
		$role->add_cap( 'read_private_passwords' );
		$role->add_cap( 'edit_password' );
		$role->add_cap( 'delete_password' );
	}

	/**
	 * Only allow roles with publish_password capability to see
	 * Password post type admin UI
	 * @since 0.1.0
	 */
	public function manage_ui_access() {
		if ( ! current_user_can( 'publish_password' ) ) {
			remove_menu_page( 'edit.php?post_type=gps_password' );
		}
	}

	/**
	 * Adds custom meta boxes for this post type.
	 * Allows user to store the password, and other custom fields
	 * @since 0.1.0
	 */
	public function add_meta_box() {

		add_meta_box(
			'gps_password_meta',
			__( 'Section Password', 'password-protect-sections' ),
			array( &$this, 'password_meta_view' ),
			'gps_password',
			'normal',
			'high'
		);

	}

	/**
	 * Handles rendering the custom meta fields for Password post types
	 *
	 * @param type $post WP_Post object for password being edited
	 *
	 * @since 0.1.0
	 */
	public function password_meta_view( $post ) {

		wp_nonce_field( 'save_password_in_password_type', 'gps_password_meta_nonce' );
		require_once( dirname( __FILE__ ) . '/../views/password-meta-view.php' );
	}


	/**
	 *
	 * @param   int $post_id ID for thie Password being edited or Password
	 *                          that was stored
	 *
	 * @return  int $post_id
	 * @since 0.1.0
	 */
	public function save_meta_box_data( $post_id ) {

		// first, check to be sure we should be saving the data
		if ( $this->is_okay_to_save( $post_id ) ) {

			// Now we know it's safe to try to save the data,
			$password_input = filter_input( INPUT_POST, 'password_input' );
			if ( $password_input ) {
				update_post_meta( $post_id, '_gps_password', $password_input );
			}

			$password_failed_message = filter_input( INPUT_POST, 'password_failed_message' );
			if ( $password_failed_message ) {
				update_post_meta( $post_id, '_gps_password_failed_message', $password_failed_message );
			}

		}

		return $post_id;
	}

	/**
	 * Use nonce to verify someone isn't trying to hack the system with a bogus
	 * password post.  Match the returned post data with the view we generated.
	 *
	 * @return  boolean     True if nonce is verified, otherwise false
	 * @since 0.2.1
	 */
	private function nonce_is_verified() {
		return wp_verify_nonce( filter_input( INPUT_POST, 'gps_password_meta_nonce' ),
			'save_password_in_password_type' );
	}

	/**
	 * verify if this is an auto save routine. If it is, our form has not been submitted,
	 * so we dont want to do anything
	 *
	 * @return   boolean     True if this save is an autosave, otherwise false
	 * @since 0.2.1
	 */
	private function is_doing_autosave() {
		return defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE;
	}

	/**
	 * Check user permissions to verify user is allowed to edit the password object
	 *
	 * @param   int $post_id The post id for which we are checking permissions
	 *
	 * @return  boolean     True if the user can edit passwords, otherwise false
	 * @since 0.2.1
	 */
	private function user_can_save_data( $post_id ) {
		return current_user_can( 'edit_password', $post_id );
	}

	/**
	 * A condition used by is_okay_to_save. We need to make sure we've got the
	 * right post type before trying to save.
	 *
	 * @param   WP_Post $post The post we are verifying whether it is a Password
	 *                          post type
	 *
	 * @return  boolean True if the post is a Password post type, otherwise false
	 * @since 0.2.1
	 */
	private function is_password_post_type( $post ) {
		$post->post_type === 'gps_password';
	}

	/**
	 * Convenience function that tells us when all the conditions are met to save
	 * the password post type fields.
	 *
	 * @param int $post_id id of post we are checking to see if we can save it
	 *
	 * @return boolean  true when the conditions are all met indicating it is safe
	 *                  and appropriate to save the password fields, otherwise false.
	 * @since 0.2.1
	 */
	private function is_okay_to_save( $post_id ) {

		$post = get_post( $post_id );

		return $post &&
		       $this->nonce_is_verified() &&
		       ! $this->is_doing_autosave() &&
		       $this->user_can_save_data( $post_id ) &&
		       $this->is_password_post_type( $post );
	}

}