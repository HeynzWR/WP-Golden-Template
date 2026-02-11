<?php
/**
 * Map Block - ACF Fields
 *
 * @package GoldenTemplate
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Register ACF fields for Map Block
 */
function golden_template_map_block_fields() {
	if ( ! function_exists( 'acf_add_local_field_group' ) ) {
		return;
	}

	// Define the 6 fixed locations
	$locations = array(
		'dfw_houston' => 'DFW/Houston',
		'austin'       => 'Austin',
		'metro_dc'     => 'Metro DC',
		'boston'       => 'Boston',
		'phoenix'      => 'Phoenix',
		'atlanta'      => 'Atlanta',
	);

	// Build location choices for select field
	$location_choices = array();
	foreach ( $locations as $key => $label ) {
		$location_choices[ $key ] = $label;
	}

	$fields = array(
		// Content Tab
		array(
			'key'   => 'field_golden_template_map_block_content_tab',
			'label' => 'Content',
			'type'  => 'tab',
		),
		// Warning Message
		array(
			'key'           => 'field_golden_template_map_block_warning',
			'label'         => '',
			'name'          => '',
			'type'          => 'message',
			'message'       => '<div style="background-color: #fff3cd; border-left: 4px solid #ffc107; padding: 1rem; margin: 1rem 0;">
				<p style="margin: 0 0 0.5rem 0; font-weight: 600; color: #856404;"><strong>⚠️ Important</strong></p>
				<p style="margin: 0; color: #856404;">Map regions are <strong>fixed SVGs</strong> and cannot be modified via CMS. Adding or updating a card does <strong>not</strong> create or modify map regions. All <strong>6 cards must be populated</strong> and cannot be added or removed.</p>
			</div>',
			'new_lines'     => '',
			'esc_html'      => 0,
		),
		// Location Cards Repeater
		array(
			'key'          => 'field_golden_template_map_block_location_cards',
			'label'        => 'Location Cards',
			'name'         => 'location_cards',
			'type'         => 'repeater',
			'button_label' => 'Add Location Card',
			'required'     => 1,
			'min'          => 6,
			'max'          => 6,
			'layout'       => 'block',
			'collapsed'    => 'field_golden_template_map_block_title',
			'instructions' => 'Add exactly 6 location cards in this order: 1) DFW/Houston, 2) Austin, 3) Metro DC, 4) Boston, 5) Phoenix, 6) Atlanta. Cards are automatically mapped to map regions based on their position.',
			'sub_fields'   => array(
				array(
					'key'         => 'field_golden_template_map_block_title',
					'label'       => 'Title',
					'name'        => 'title',
					'type'        => 'text',
					'required'    => 1,
					'maxlength'   => 100,
					'placeholder' => 'e.g., Dallas Office, Austin Branch',
					'instructions' => 'Required. Enter any label for this location (e.g., "Dallas Office", "Austin Branch", etc.). Maximum 100 characters.',
				),
				array(
					'key'         => 'field_golden_template_map_block_name',
					'label'       => 'Name',
					'name'        => 'name',
					'type'        => 'text',
					'required'    => 1,
					'maxlength'   => 100,
					'placeholder' => 'e.g., John Doe',
					'instructions' => 'Required. Person name (e.g., John Doe). Maximum 100 characters.',
				),
				array(
					'key'         => 'field_golden_template_map_block_subtitle',
					'label'       => 'Subtitle',
					'name'        => 'subtitle',
					'type'        => 'text',
					'required'    => 1,
					'maxlength'   => 100,
					'placeholder' => 'e.g., Regional Partner',
					'instructions' => 'Required. Used for role/title (e.g., Regional Partner). Maximum 100 characters.',
				),
				array(
					'key'         => 'field_golden_template_map_block_content_info_title',
					'label'       => 'Content Info Title',
					'name'        => 'content_info_title',
					'type'        => 'text',
					'required'    => 1,
					'maxlength'   => 100,
					'placeholder' => 'e.g., Office Info',
					'instructions' => 'Required. Used for info title (e.g., Office Info). Maximum 100 characters.',
				),
				array(
					'key'          => 'field_golden_template_map_block_content_info',
					'label'        => 'Content Info',
					'name'         => 'content_info',
					'type'         => 'wysiwyg',
					'required'     => 1,
					'instructions' => 'Required. Content for this location. Supports bold text, italics, and links only. Maximum 500 characters.',
					'tabs'         => 'visual',
					'toolbar'      => 'minimal',
					'media_upload' => 0,
				),
				array(
					'key'          => 'field_golden_template_map_block_cta',
					'label'        => 'CTA',
					'name'         => 'cta',
					'type'         => 'link',
					'instructions' => 'Optional. Call-to-action link for this location.',
					'required'     => 0,
					'return_format' => 'array',
				),
			),
		),
	);

	acf_add_local_field_group(
		array(
			'key'    => 'group_golden_template_map_block',
			'title'  => 'Map Block',
			'fields' => $fields,
			'location' => array(
				array(
					array(
						'param'    => 'block',
						'operator' => '==',
						'value'    => 'acf/map-block',
					),
				),
			),
		)
	);
}
add_action( 'acf/init', 'golden_template_map_block_fields' );

/**
 * Validate map block title character limit (max 100 characters)
 *
 * @param bool|string $valid Whether the field value is valid.
 * @param string      $value The field value.
 * @param array       $field The field array.
 * @param string      $input_name The input name (for repeater fields). // phpcs:ignore VariableAnalysis.CodeAnalysis.VariableAnalysis.UnusedVariable -- Required by ACF filter signature.
 * @return bool|string True if valid, error message if invalid.
 */
function golden_template_map_block_validate_title( $valid, $value, $field, $input_name ) { // phpcs:ignore VariableAnalysis.CodeAnalysis.VariableAnalysis.UnusedVariable -- Required by ACF filter signature.
	// Only apply to map block title field.
	if ( ! isset( $field['key'] ) || 'field_golden_template_map_block_title' !== $field['key'] ) {
		return $valid;
	}

	// Count characters.
	$text_length = mb_strlen( $value );
	if ( $text_length > 100 ) {
		$valid = sprintf(
			/* translators: %d: Maximum character limit */
			__( 'Title must not exceed 100 characters. Current length: %d characters.', 'golden-template' ),
			$text_length
		);
	}
	return $valid;
}
add_filter( 'acf/validate_value', 'golden_template_map_block_validate_title', 10, 4 );

/**
 * Validate map block subtitle character limit (max 100 characters)
 *
 * @param bool|string $valid Whether the field value is valid.
 * @param string      $value The field value.
 * @param array       $field The field array.
 * @param string      $input_name The input name (for repeater fields). // phpcs:ignore VariableAnalysis.CodeAnalysis.VariableAnalysis.UnusedVariable -- Required by ACF filter signature.
 * @return bool|string True if valid, error message if invalid.
 */
function golden_template_map_block_validate_subtitle( $valid, $value, $field, $input_name ) { // phpcs:ignore VariableAnalysis.CodeAnalysis.VariableAnalysis.UnusedVariable -- Required by ACF filter signature.
	// Only apply to map block subtitle field.
	if ( ! isset( $field['key'] ) || 'field_golden_template_map_block_subtitle' !== $field['key'] ) {
		return $valid;
	}

	// Count characters.
	$text_length = mb_strlen( $value );
	if ( $text_length > 100 ) {
		$valid = sprintf(
			/* translators: %d: Maximum character limit */
			__( 'Subtitle must not exceed 100 characters. Current length: %d characters.', 'golden-template' ),
			$text_length
		);
	}
	return $valid;
}
add_filter( 'acf/validate_value', 'golden_template_map_block_validate_subtitle', 10, 4 );

/**
 * Validate map block name character limit (max 100 characters)
 *
 * @param bool|string $valid Whether the field value is valid.
 * @param string      $value The field value.
 * @param array       $field The field array.
 * @param string      $input_name The input name (for repeater fields). // phpcs:ignore VariableAnalysis.CodeAnalysis.VariableAnalysis.UnusedVariable -- Required by ACF filter signature.
 * @return bool|string True if valid, error message if invalid.
 */
function golden_template_map_block_validate_name( $valid, $value, $field, $input_name ) { // phpcs:ignore VariableAnalysis.CodeAnalysis.VariableAnalysis.UnusedVariable -- Required by ACF filter signature.
	// Only apply to map block name field.
	if ( ! isset( $field['key'] ) || 'field_golden_template_map_block_name' !== $field['key'] ) {
		return $valid;
	}

	// Count characters.
	$text_length = mb_strlen( $value );
	if ( $text_length > 100 ) {
		$valid = sprintf(
			/* translators: %d: Maximum character limit */
			__( 'Name must not exceed 100 characters. Current length: %d characters.', 'golden-template' ),
			$text_length
		);
	}
	return $valid;
}
add_filter( 'acf/validate_value', 'golden_template_map_block_validate_name', 10, 4 );

/**
 * Validate map block content info title character limit (max 100 characters)
 *
 * @param bool|string $valid Whether the field value is valid.
 * @param string      $value The field value.
 * @param array       $field The field array.
 * @param string      $input_name The input name (for repeater fields). // phpcs:ignore VariableAnalysis.CodeAnalysis.VariableAnalysis.UnusedVariable -- Required by ACF filter signature.
 * @return bool|string True if valid, error message if invalid.
 */
function golden_template_map_block_validate_content_info_title( $valid, $value, $field, $input_name ) { // phpcs:ignore VariableAnalysis.CodeAnalysis.VariableAnalysis.UnusedVariable -- Required by ACF filter signature.
	// Only apply to map block content info title field.
	if ( ! isset( $field['key'] ) || 'field_golden_template_map_block_content_info_title' !== $field['key'] ) {
		return $valid;
	}

	// Count characters.
	$text_length = mb_strlen( $value );
	if ( $text_length > 100 ) {
		$valid = sprintf(
			/* translators: %d: Maximum character limit */
			__( 'Content Info Title must not exceed 100 characters. Current length: %d characters.', 'golden-template' ),
			$text_length
		);
	}
	return $valid;
}
add_filter( 'acf/validate_value', 'golden_template_map_block_validate_content_info_title', 10, 4 );

