<?php
/**
 * Register Project Taxonomies
 *
 * Registers custom taxonomies for the Projects CPT:
 * - By State (project_state)
 * - By Type (project_type)
 *
 * @package JLBPartners
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Register Project State Taxonomy
 */
function jlbpartners_register_project_state_taxonomy() {

	$labels = array(
		'name'                       => _x( 'By State', 'Taxonomy General Name', 'jlbpartners' ),
		'singular_name'              => _x( 'State', 'Taxonomy Singular Name', 'jlbpartners' ),
		'menu_name'                  => __( 'By State', 'jlbpartners' ),
		'all_items'                  => __( 'All States', 'jlbpartners' ),
		'parent_item'                => __( 'Parent State', 'jlbpartners' ),
		'parent_item_colon'          => __( 'Parent State:', 'jlbpartners' ),
		'new_item_name'              => __( 'New State Name', 'jlbpartners' ),
		'add_new_item'               => __( 'Add New State', 'jlbpartners' ),
		'edit_item'                  => __( 'Edit State', 'jlbpartners' ),
		'update_item'                => __( 'Update State', 'jlbpartners' ),
		'view_item'                  => __( 'View State', 'jlbpartners' ),
		'separate_items_with_commas' => __( 'Separate states with commas', 'jlbpartners' ),
		'add_or_remove_items'        => __( 'Add or remove states', 'jlbpartners' ),
		'choose_from_most_used'      => __( 'Choose from the most used', 'jlbpartners' ),
		'popular_items'              => __( 'Popular States', 'jlbpartners' ),
		'search_items'               => __( 'Search States', 'jlbpartners' ),
		'not_found'                  => __( 'Not Found', 'jlbpartners' ),
		'no_terms'                   => __( 'No states', 'jlbpartners' ),
		'items_list'                 => __( 'States list', 'jlbpartners' ),
		'items_list_navigation'      => __( 'States list navigation', 'jlbpartners' ),
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
function jlbpartners_register_project_type_taxonomy() {

	$labels = array(
		'name'                       => _x( 'By Type', 'Taxonomy General Name', 'jlbpartners' ),
		'singular_name'              => _x( 'Type', 'Taxonomy Singular Name', 'jlbpartners' ),
		'menu_name'                  => __( 'By Type', 'jlbpartners' ),
		'all_items'                  => __( 'All Types', 'jlbpartners' ),
		'parent_item'                => __( 'Parent Type', 'jlbpartners' ),
		'parent_item_colon'          => __( 'Parent Type:', 'jlbpartners' ),
		'new_item_name'              => __( 'New Type Name', 'jlbpartners' ),
		'add_new_item'               => __( 'Add New Type', 'jlbpartners' ),
		'edit_item'                  => __( 'Edit Type', 'jlbpartners' ),
		'update_item'                => __( 'Update Type', 'jlbpartners' ),
		'view_item'                  => __( 'View Type', 'jlbpartners' ),
		'separate_items_with_commas' => __( 'Separate types with commas', 'jlbpartners' ),
		'add_or_remove_items'        => __( 'Add or remove types', 'jlbpartners' ),
		'choose_from_most_used'      => __( 'Choose from the most used', 'jlbpartners' ),
		'popular_items'              => __( 'Popular Types', 'jlbpartners' ),
		'search_items'               => __( 'Search Types', 'jlbpartners' ),
		'not_found'                  => __( 'Not Found', 'jlbpartners' ),
		'no_terms'                   => __( 'No types', 'jlbpartners' ),
		'items_list'                 => __( 'Types list', 'jlbpartners' ),
		'items_list_navigation'      => __( 'Types list navigation', 'jlbpartners' ),
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
add_action( 'init', 'jlbpartners_register_project_state_taxonomy', 1 );
add_action( 'init', 'jlbpartners_register_project_type_taxonomy', 1 );

