<?php
/**
 * Register Projects Custom Post Type
 *
 * @package GoldenTemplate
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Register Projects CPT
 */
function golden_template_register_projects_cpt() {

	$labels = array(
		'name'                  => _x( 'Projects', 'Post Type General Name', 'golden-template' ),
		'singular_name'         => _x( 'Project', 'Post Type Singular Name', 'golden-template' ),
		'menu_name'             => __( 'Projects', 'golden-template' ),
		'name_admin_bar'        => __( 'Project', 'golden-template' ),
		'archives'              => __( 'Project Archives', 'golden-template' ),
		'attributes'            => __( 'Project Attributes', 'golden-template' ),
		'parent_item_colon'     => __( 'Parent Project:', 'golden-template' ),
		'all_items'             => __( 'All Projects', 'golden-template' ),
		'add_new_item'          => __( 'Add New Project', 'golden-template' ),
		'add_new'               => __( 'Add New', 'golden-template' ),
		'new_item'              => __( 'New Project', 'golden-template' ),
		'edit_item'             => __( 'Edit Project', 'golden-template' ),
		'update_item'           => __( 'Update Project', 'golden-template' ),
		'view_item'             => __( 'View Project', 'golden-template' ),
		'view_items'            => __( 'View Projects', 'golden-template' ),
		'search_items'          => __( 'Search Project', 'golden-template' ),
		'not_found'             => __( 'Not found', 'golden-template' ),
		'not_found_in_trash'    => __( 'Not found in Trash', 'golden-template' ),
		'featured_image'        => __( 'Featured Image', 'golden-template' ),
		'set_featured_image'    => __( 'Set featured image', 'golden-template' ),
		'remove_featured_image' => __( 'Remove featured image', 'golden-template' ),
		'use_featured_image'    => __( 'Use as featured image', 'golden-template' ),
		'insert_into_item'      => __( 'Insert into project', 'golden-template' ),
		'uploaded_to_this_item' => __( 'Uploaded to this project', 'golden-template' ),
		'items_list'            => __( 'Projects list', 'golden-template' ),
		'items_list_navigation' => __( 'Projects list navigation', 'golden-template' ),
		'filter_items_list'     => __( 'Filter projects list', 'golden-template' ),
	);
	$args = array(
		'label'                 => __( 'Project', 'golden-template' ),
		'description'           => __( 'Projects Portfolio', 'golden-template' ),
		'labels'                => $labels,
		'supports'              => array( 'title', 'editor', 'excerpt', 'thumbnail', 'revisions' ),
		'taxonomies'            => array( 'project_state', 'project_type', 'post_tag' ),
		'hierarchical'          => false,
		'public'                => true,
		'show_ui'               => true,
		'show_in_menu'          => true,
		'menu_position'         => 5,
		'menu_icon'             => 'dashicons-building',
		'show_in_admin_bar'     => true,
		'show_in_nav_menus'     => true,
		'can_export'            => true,
		'has_archive'           => true,
		'exclude_from_search'   => false,
		'publicly_queryable'    => true,
		'capability_type'       => 'page',
		'show_in_rest'          => true, // Enable Gutenberg editor
	);
	register_post_type( 'projects', $args );

}
add_action( 'init', 'golden_template_register_projects_cpt', 0 );

/**
 * Modify the main query for projects archive
 */
function golden_template_modify_projects_query( $query ) {
	// Only affect the main query on the projects archive, not admin or other queries
	if ( ! is_admin() && $query->is_main_query() && is_post_type_archive( 'projects' ) ) {
		// Get custom posts per page setting
		$projects_per_page = absint( get_option( 'golden_template_projects_per_page', 2 ) );
		$query->set( 'posts_per_page', $projects_per_page );
		
		// Order projects alphabetically by title (project name)
		$query->set( 'orderby', 'title' );
		$query->set( 'order', 'ASC' );
		
		// Handle taxonomy filters from URL parameters - sanitize input
		// phpcs:ignore WordPress.Security.NonceVerification.Recommended,WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- Reading URL parameters in pre_get_posts, sanitized below
		$selected_states_raw = isset( $_GET['states'] ) ? wp_unslash( $_GET['states'] ) : '';
		// phpcs:ignore WordPress.Security.NonceVerification.Recommended,WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- Reading URL parameters in pre_get_posts, sanitized below
		$selected_types_raw  = isset( $_GET['types'] ) ? wp_unslash( $_GET['types'] ) : '';
		
		$selected_states = ! empty( $selected_states_raw ) ? array_map( 'sanitize_text_field', explode( ',', $selected_states_raw ) ) : array();
		$selected_types  = ! empty( $selected_types_raw ) ? array_map( 'sanitize_text_field', explode( ',', $selected_types_raw ) ) : array();
		
		// Remove empty values
		$selected_states = array_filter( $selected_states );
		$selected_types  = array_filter( $selected_types );
		
		// Apply taxonomy filters if any are selected
		if ( ! empty( $selected_states ) || ! empty( $selected_types ) ) {
			$tax_query = array( 'relation' => 'AND' );
			
			if ( ! empty( $selected_states ) ) {
				$tax_query[] = array(
					'taxonomy' => 'project_state',
					'field'    => 'slug',
					'terms'    => $selected_states,
				);
			}
			
			if ( ! empty( $selected_types ) ) {
				$tax_query[] = array(
					'taxonomy' => 'project_type',
					'field'    => 'slug',
					'terms'    => $selected_types,
				);
			}
			
			$query->set( 'tax_query', $tax_query );
		}
	}
}
add_action( 'pre_get_posts', 'golden_template_modify_projects_query' );
