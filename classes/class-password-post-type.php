<?php

/* 
 * This class contains the Password custom post type and related functions
 */

class Password_Custom_Post_Type {
    
    var $capability = "edit_passwords";
    
    function __construct(){
        
        //Add capability to use this plugin to administrator by default
        add_action( 'admin_init', array( &$this, 'add_capability_to_administrator_role' ) );
        
        //Add this post type
        add_action( 'init', array( &$this, 'create_post_type' ) );
        
        //Add styling for admin pages
        add_action('admin_enqueue_scripts', array( &$this, 'enqueue_admin_style'));
        
        //Only show admin menu items to those with access
        add_action( 'admin_menu', array( &$this, 'manage_ui_access' ) );
        
        //Add the custom meta boxes for this post type
        add_action( 'add_meta_boxes', array( &$this, 'add_meta_box' ) );
        
        //Handle saving meta box inputs
        add_action('save_post', array(&$this,'save_meta_box_data') );
        
        
    }
    
    
    public function enqueue_admin_style(){
        wp_enqueue_style( 'gps_password_admin_style', plugins_url('password-protect-sections/css/style-admin.css') );
    }
    
    /**
     * Defines the Password custom Post Type
     */
    public function create_post_type() {
        
        //first define the capabilities in the context of this post type
        $capabilities = array(
            'publish_posts' => 'publish_password',
            'edit_posts' => 'edit_password',
            'edit_others_posts' => 'edit_others_passwords',
            'delete_posts' => 'delete_passwords',
            'delete_others_posts' => 'delete_others_passwords',
            'read_private_posts' => 'read_private_passwords',
            'edit_post' => 'edit_password',
            'delete_post' => 'delete_password',
            'read_post' => 'read_password'
        );
        
        $labels = array(
            'name' => __( 'Passwords' ),
            'singular_name' => __( 'Password' ),
            'not_found' => 'No Passwords Defined',
            'not_found_in_trash' => 'No Passwords Defined in Trash',
        );
        
	register_post_type( 'gps_password', array (
            'labels' => $labels,
            'description' => 'A Password instance uniformly applies protection to all sections of content surrounded by the shortcode tags with that Password\'s ID',
            'public' => false,
            'has_archive' => false,
            'show_ui' => true,
            'hierarchical' => false,
            'capabilities'=> $capabilities,
            'menu_position' => 85,
            'supports' =>  array( 'title','editor' ),
            'map_meta_cap' => true,
            'menu_icon' => 'dashicons-lock',
        ) );
    }
    
    /**
     * Add capability to use this plugin to administrator
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
     */
    public function manage_ui_access(){
        if( !current_user_can( 'publish_password' ) )
            remove_menu_page( 'edit.php?post_type=gps_password' );
    }
    
    /**
     * Adds custom meta boxes for this post type.
     * Allows user to store the password, and other custom fields
     */
    public function add_meta_box(){
        
        add_meta_box(
                'gps_password_meta',
                __( 'Section Password', 'gps_password'),
                array( &$this, 'password_meta_view'),
                'gps_password',
                'normal',
                'high'
        );
        
    }
    
    /**
     * Handles rendering the custom meta fields for Password post types
     * @param type $post WP_Post object for password being edited
     */
    public function password_meta_view( $post ){
        
        wp_nonce_field( 'save_password_in_password_type', 'gps_password_meta_nonce' );
        require_once (dirname( __FILE__ ) . '/../views/password-meta-view.php');
    }
    
    
    /**
     * 
     * @param type $post_id  ID for thie Password being edited
     * @return type $post_id or Password that was stored
     */
    public function save_meta_box_data( $post_id ){
                
        // verify this came from our screen and with proper authorization.
        if ( !wp_verify_nonce( $_POST['gps_password_meta_nonce'], 'save_password_in_password_type' )) {
            return $post_id;
        }
        
        
        // verify if this is an auto save routine. If it is our form has not been submitted, so we dont want to do anything
        if ( defined('DOING_AUTOSAVE') && DOING_AUTOSAVE )
            return $post_id;

        // Check permissions
        if ( !current_user_can( 'edit_password', $post_id ) )
            return $post_id;


        // OK, we're authenticated: we need to find and save the data  
        $post = get_post($post_id);
        if ($post->post_type == 'gps_password') {

            update_post_meta($post_id, '_gps_password', $_POST['password_input'] );
            update_post_meta($post_id, '_gps_password_failed_message', $_POST['password_failed_message']);
        }
        return $post_id;
    }
    
}