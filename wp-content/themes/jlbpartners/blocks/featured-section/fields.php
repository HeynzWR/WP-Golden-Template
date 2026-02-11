<?php
/**
 * Featured Section Block - ACF Fields
 *
 * @package JLBPartners
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Register ACF fields for Featured Section Block
 */
function jlbpartners_featured_projects_fields() {
	if ( ! function_exists( 'acf_add_local_field_group' ) ) {
		return;
	}

	acf_add_local_field_group(
		array(
			'key'    => 'group_featured_projects',
			'title'  => 'Featured Section',
			'fields' => array(
				// Section Title
				array(
					'key'          => 'field_featured_projects_title',
					'label'        => 'Section Title',
					'name'         => 'featured_projects_title',
					'type'         => 'text',
					'instructions' => 'Optional. Enter the main heading for the featured projects section. Keep it concise and descriptive (maximum 100 characters).',
					'required'     => 0,
					'maxlength'   => 100,
					'placeholder' => 'Featured Projects',
					'default_value' => '',
				),

				// Display Mode
				array(
					'key'           => 'field_display_mode',
					'label'         => 'Display Mode',
					'name'          => 'display_mode',
					'type'          => 'radio',
					'instructions'  => 'Choose how to select projects. "Show All Projects" will automatically display the 5 latest published projects (minimum 2 required). "Select Manually" allows you to choose 2-5 specific projects.',
					'choices'       => array(
						'all_featured' => 'Show All Projects',
						'manual'       => 'Select Projects Manually',
					),
					'default_value' => 'all_featured',
					'layout'        => 'vertical',
				),

				// Manual Selection (Conditional)
				array(
					'key'               => 'field_selected_projects',
					'label'             => 'Select Projects',
					'name'              => 'selected_projects',
					'type'              => 'relationship',
					'instructions'      => 'Required when display mode is "Select Manually". Select 2-5 projects to display. Search and click to add multiple projects. You must select at least 2 projects and can select up to 5 projects.',
					'required'          => 0,
					'post_type'         => array( 'projects' ),
					'taxonomy'          => '',
					'filters'           => array( 'search' ),
					'elements'          => array( 'featured_image' ),
					'min'               => 2,
					'max'               => 5,
					'return_format'     => 'object',
					'conditional_logic' => array(
						array(
							array(
								'field'    => 'field_display_mode',
								'operator' => '==',
								'value'    => 'manual',
							),
						),
					),
				),
			),
			'location' => array(
				array(
					array(
						'param'    => 'block',
						'operator' => '==',
						'value'    => 'acf/featured-section',
					),
				),
			),
		)
	);
}
add_action( 'acf/init', 'jlbpartners_featured_projects_fields' );
