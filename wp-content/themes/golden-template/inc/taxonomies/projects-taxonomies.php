<?php
/**
 * Register Project Taxonomies
 *
 * Registers custom taxonomies for the Projects CPT:
 * - By State (project_state)
 * - By Type (project_type)
 *
 * @package GoldenTemplate
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Register Project State Taxonomy
 */
function golden_template_register_project_state_taxonomy() {

	$labels = array(
		'name'                       => _x( 'By State', 'Taxonomy General Name', 'golden-template' ),
		'singular_name'              => _x( 'State', 'Taxonomy Singular Name', 'golden-template' ),
		'menu_name'                  => __( 'By State', 'golden-template' ),
		'all_items'                  => __( 'All States', 'golden-template' ),
		'parent_item'                => __( 'Parent State', 'golden-template' ),
		'parent_item_colon'          => __( 'Parent State:', 'golden-template' ),
		'new_item_name'              => __( 'New State Name', 'golden-template' ),
		'add_new_item'               => __( 'Add New State', 'golden-template' ),
		'edit_item'                  => __( 'Edit State', 'golden-template' ),
		'update_item'                => __( 'Update State', 'golden-template' ),
		'view_item'                  => __( 'View State', 'golden-template' ),
		'separate_items_with_commas' => __( 'Separate states with commas', 'golden-template' ),
		'add_or_remove_items'        => __( 'Add or remove states', 'golden-template' ),
		'choose_from_most_used'      => __( 'Choose from the most used', 'golden-template' ),
		'popular_items'              => __( 'Popular States', 'golden-template' ),
		'search_items'               => __( 'Search States', 'golden-template' ),
		'not_found'                  => __( 'Not Found', 'golden-template' ),
		'no_terms'                   => __( 'No states', 'golden-template' ),
		'items_list'                 => __( 'States list', 'golden-template' ),
		'items_list_navigation'      => __( 'States list navigation', 'golden-template' ),
	);

	$args = array(
		'labels'                     => $labels,
		'hierarchical'               => true,
		'public'                     => true,
		'show_ui'                    => true,
		'show_admin_column'          => true,
		'show_in_nav_menus'          => true,
		'show_tagcloud'              => false,
		'show_in_rest'               => true,
		'rewrite'                    => array(
			'slug' => 'project-state',
		),
	);

	register_taxonomy( 'project_state', array( 'projects' ), $args );

}

/**
 * Register Project Type Taxonomy
 */
function golden_template_register_project_type_taxonomy() {

	$labels = array(
		'name'                       => _x( 'By Type', 'Taxonomy General Name', 'golden-template' ),
		'singular_name'              => _x( 'Type', 'Taxonomy Singular Name', 'golden-template' ),
		'menu_name'                  => __( 'By Type', 'golden-template' ),
		'all_items'                  => __( 'All Types', 'golden-template' ),
		'parent_item'                => __( 'Parent Type', 'golden-template' ),
		'parent_item_colon'          => __( 'Parent Type:', 'golden-template' ),
		'new_item_name'              => __( 'New Type Name', 'golden-template' ),
		'add_new_item'               => __( 'Add New Type', 'golden-template' ),
		'edit_item'                  => __( 'Edit Type', 'golden-template' ),
		'update_item'                => __( 'Update Type', 'golden-template' ),
		'view_item'                  => __( 'View Type', 'golden-template' ),
		'separate_items_with_commas' => __( 'Separate types with commas', 'golden-template' ),
		'add_or_remove_items'        => __( 'Add or remove types', 'golden-template' ),
		'choose_from_most_used'      => __( 'Choose from the most used', 'golden-template' ),
		'popular_items'              => __( 'Popular Types', 'golden-template' ),
		'search_items'               => __( 'Search Types', 'golden-template' ),
		'not_found'                  => __( 'Not Found', 'golden-template' ),
		'no_terms'                   => __( 'No types', 'golden-template' ),
		'items_list'                 => __( 'Types list', 'golden-template' ),
		'items_list_navigation'      => __( 'Types list navigation', 'golden-template' ),
	);

	$args = array(
		'labels'                     => $labels,
		'hierarchical'               => true,
		'public'                     => true,
		'show_ui'                    => true,
		'show_admin_column'          => true,
		'show_in_nav_menus'          => true,
		'show_tagcloud'              => false,
		'show_in_rest'               => true,
		'rewrite'                    => array(
			'slug' => 'project-type',
		),
	);

	register_taxonomy( 'project_type', array( 'projects' ), $args );

}

// Register taxonomies on init, after CPT registration
add_action( 'init', 'golden_template_register_project_state_taxonomy', 1 );
add_action( 'init', 'golden_template_register_project_type_taxonomy', 1 );

