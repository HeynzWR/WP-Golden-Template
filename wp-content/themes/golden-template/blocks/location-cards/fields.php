<?php
/**
 * Location Cards Block - ACF Fields
 *
 * @package GoldenTemplate
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Register ACF fields for Location Cards Block
 */
function golden_template_location_cards_fields() {
	if ( ! function_exists( 'acf_add_local_field_group' ) ) {
		return;
	}

	acf_add_local_field_group(
		array(
			'key'    => 'group_golden_template_location_cards',
			'title'  => 'Location Cards',
			'fields' => array(
				// Content Tab
				array(
					'key'   => 'field_golden_template_location_cards_content_tab',
					'label' => 'Content',
					'type'  => 'tab',
				),
				array(
					'key'         => 'field_golden_template_location_cards_title',
					'label'       => 'Title',
					'name'        => 'title',
					'type'        => 'text',
					'required'    => 0,
					'maxlength'   => 100,
					'instructions' => 'Optional. Main title for the location cards section. Maximum 100 characters.',
				),
				array(
					'key'          => 'field_golden_template_location_cards_locations',
					'label'        => 'Locations',
					'name'         => 'locations',
					'type'         => 'repeater',
					'layout'       => 'block',
					'collapsed'    => 'field_golden_template_location_card_title',
					'button_label' => 'Add Location',
					'required'     => 1,
					'min'          => 1,
					'instructions' => 'Required. Add one or more locations. Each location requires a title and content.',
					'sub_fields'  => array(
						array(
							'key'         => 'field_golden_template_location_card_title',
							'label'       => 'Title',
							'name'        => 'title',
							'type'        => 'text',
							'required'    => 1,
							'maxlength'   => 100,
							'instructions' => 'Required. Used for location name (e.g., Dallas, Austin, Metro DC). Maximum 100 characters.',
						),
						array(
							'key'          => 'field_golden_template_location_card_content',
							'label'        => 'Content',
							'name'         => 'content',
							'type'         => 'wysiwyg',
							'required'     => 1,
							'instructions' => 'Required. Content for this location card. Supports bold text, italics, and links only. Maximum 500 characters.',
							'tabs'         => 'visual',
							'toolbar'      => 'minimal',
							'media_upload' => 0,
						),
					),
				),
			),
			'location' => array(
				array(
					array(
						'param'    => 'block',
						'operator' => '==',
						'value'    => 'acf/location-cards',
					),
				),
			),
		)
	);
}
add_action( 'acf/init', 'golden_template_location_cards_fields' );

/**
 * Validate location cards block title character limit (max 100 characters)
 *
 * @param bool|string $valid Whether the field value is valid.
 * @param string      $value The field value.
 * @param array       $field The field array.
 * @param string      $input_name The input name. // phpcs:ignore VariableAnalysis.CodeAnalysis.VariableAnalysis.UnusedVariable -- Required by ACF filter signature.
 * @return bool|string True if valid, error message if invalid.
 */
function golden_template_location_cards_validate_block_title( $valid, $value, $field, $input_name ) { // phpcs:ignore VariableAnalysis.CodeAnalysis.VariableAnalysis.UnusedVariable -- Required by ACF filter signature.
	// Only apply to location cards block title field.
	if ( ! isset( $field['key'] ) || 'field_golden_template_location_cards_title' !== $field['key'] ) {
		return $valid;
	}

	// Skip validation if empty (field is optional).
	if ( empty( $value ) ) {
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
add_filter( 'acf/validate_value', 'golden_template_location_cards_validate_block_title', 10, 4 );

/**
 * Validate location card title character limit (max 100 characters)
 *
 * @param bool|string $valid Whether the field value is valid.
 * @param string      $value The field value.
 * @param array       $field The field array.
 * @param string      $input_name The input name (for repeater fields). // phpcs:ignore VariableAnalysis.CodeAnalysis.VariableAnalysis.UnusedVariable -- Required by ACF filter signature.
 * @return bool|string True if valid, error message if invalid.
 */
function golden_template_location_cards_validate_title( $valid, $value, $field, $input_name ) { // phpcs:ignore VariableAnalysis.CodeAnalysis.VariableAnalysis.UnusedVariable -- Required by ACF filter signature.
	// Only apply to location card title field.
	if ( ! isset( $field['key'] ) || 'field_golden_template_location_card_title' !== $field['key'] ) {
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
add_filter( 'acf/validate_value', 'golden_template_location_cards_validate_title', 10, 4 );
