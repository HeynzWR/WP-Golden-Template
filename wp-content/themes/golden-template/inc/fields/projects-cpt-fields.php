<?php
/**
 * Projects CPT Fields
 *
 * @package GoldenTemplate
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Register ACF fields for Projects CPT
 */
function golden_template_projects_cpt_fields() {
	if ( ! function_exists( 'acf_add_local_field_group' ) ) {
		return;
	}

	acf_add_local_field_group(
		array(
			'key'                   => 'group_projects_cpt',
			'title'                 => 'Project Details',
			'fields'                => array(
				// Project Location
				array(
					'key'               => 'field_project_location',
					'label'             => 'Location',
					'name'              => 'project_location',
					'type'              => 'text',
					'instructions'      => 'Enter the project location. Keep it concise and descriptive (maximum 100 characters).',
					'required'          => 0,
					'maxlength'         => 100,
					'placeholder'       => 'e.g., Atlanta, GA',
					'default_value'     => '',
				),

				// Project Units
				array(
					'key'               => 'field_project_units',
					'label'             => 'Units',
					'name'              => 'project_units',
					'type'              => 'text',
					'instructions'      => 'Enter the number of units for this project. Keep it concise (maximum 50 characters).',
					'required'          => 0,
					'maxlength'         => 50,
					'placeholder'       => 'e.g., 312 Units',
					'default_value'     => '',
				),

				// External Link
				array(
					'key'               => 'field_project_external_link',
					'label'             => 'Link (Optional)',
					'name'              => 'project_external_link',
					'type'              => 'link',
					'instructions'      => 'Add an optional external link to override the default project permalink. The link text will be used as the button label.',
					'required'          => 0,
					'return_format'     => 'array',
				),
			),
			'location'              => array(
				array(
					array(
						'param'    => 'post_type',
						'operator' => '==',
						'value'    => 'projects',
					),
				),
			),
			'menu_order'            => 0,
			'position'              => 'normal',
			'style'                 => 'default',
			'label_placement'       => 'top',
			'instruction_placement' => 'label',
			'hide_on_screen'        => '',
			'active'                => true,
			'description'           => '',
		)
	);
}
add_action( 'acf/init', 'golden_template_projects_cpt_fields' );
