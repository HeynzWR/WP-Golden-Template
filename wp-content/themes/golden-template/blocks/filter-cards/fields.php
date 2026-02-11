<?php
/**
 * Filter Cards Block - ACF Fields
 *
 * @package GoldenTemplate
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Register ACF fields for Filter Cards Block
 */
function golden_template_filter_cards_fields() {
	if ( ! function_exists( 'acf_add_local_field_group' ) ) {
		return;
	}

	acf_add_local_field_group(
		array(
			'key'    => 'group_golden_template_filter_cards',
			'title'  => 'Filter Cards',
			'fields' => array(
				// Content Tab
				array(
					'key'   => 'field_golden_template_filter_cards_content_tab',
					'label' => 'Content',
					'type'  => 'tab',
				),
				array(
					'key'         => 'field_golden_template_filter_cards_heading',
					'label'       => 'Heading',
					'name'        => 'heading',
					'type'        => 'text',
					'required'    => 0,
					'maxlength'   => 100,
					'instructions' => 'Optional heading for the filter cards section. Maximum 100 characters.',
				),
				array(
					'key'          => 'field_golden_template_filter_cards_leadership_cards',
					'label'        => 'Leadership Cards',
					'name'         => 'leadership_cards',
					'type'         => 'repeater',
					'layout'       => 'block',
					'button_label' => 'Add Card',
					'collapsed'    => 'field_golden_template_filter_card_title',
					'required'     => 1,
					'min'          => 1,
					'instructions' => 'Required. Add one or more leadership cards. Each card requires a title, subtitle, and tag. Only tags that are used by cards will appear in the filter, and they will be listed alphabetically.',
					'sub_fields'  => array(
						array(
							'key'         => 'field_golden_template_filter_card_title',
							'label'       => 'Title',
							'name'        => 'title',
							'type'        => 'text',
							'required'    => 1,
							'maxlength'   => 100,
							'instructions' => 'Required. Maximum 100 characters.',
						),
						array(
							'key'         => 'field_golden_template_filter_card_subtitle',
							'label'       => 'Subtitle',
							'name'        => 'subtitle',
							'type'        => 'text',
							'required'    => 1,
							'maxlength'   => 100,
							'instructions' => 'Required. Used for role or designation. Maximum 100 characters.',
						),
						array(
							'key'          => 'field_golden_template_filter_card_content',
							'label'        => 'Content',
							'name'         => 'content',
							'type'         => 'wysiwyg',
							'required'     => 0,
							'tabs'         => 'visual',
							'toolbar'      => 'minimal',
							'media_upload' => 0,
							'instructions' => 'Optional. Add additional content for this card.',
						),
						array(
							'key'         => 'field_golden_template_filter_card_tag',
							'label'       => 'Tag',
							'name'        => 'tag',
							'type'        => 'text',
							'required'    => 1,
							'maxlength'   => 50,
							'instructions' => 'Required. Used for filtering (e.g., Region, department). Maximum 50 characters.',
						),
					),
				),
			),
			'location' => array(
				array(
					array(
						'param'    => 'block',
						'operator' => '==',
						'value'    => 'acf/filter-cards',
					),
				),
			),
		)
	);
}
add_action( 'acf/init', 'golden_template_filter_cards_fields' );

/**
 * Validate filter card title character limit (max 100 characters)
 *
 * @param bool|string $valid Whether the field value is valid.
 * @param string      $value The field value.
 * @param array       $field The field array.
 * @param string      $input_name The input name (for repeater fields). // phpcs:ignore VariableAnalysis.CodeAnalysis.VariableAnalysis.UnusedVariable -- Required by ACF filter signature.
 * @return bool|string True if valid, error message if invalid.
 */
function golden_template_filter_cards_validate_title( $valid, $value, $field, $input_name ) { // phpcs:ignore VariableAnalysis.CodeAnalysis.VariableAnalysis.UnusedVariable -- Required by ACF filter signature.
	// Only apply to filter card title field.
	if ( ! isset( $field['key'] ) || 'field_golden_template_filter_card_title' !== $field['key'] ) {
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
add_filter( 'acf/validate_value', 'golden_template_filter_cards_validate_title', 10, 4 );

/**
 * Validate filter card subtitle character limit (max 100 characters)
 *
 * @param bool|string $valid Whether the field value is valid.
 * @param string      $value The field value.
 * @param array       $field The field array.
 * @param string      $input_name The input name (for repeater fields). // phpcs:ignore VariableAnalysis.CodeAnalysis.VariableAnalysis.UnusedVariable -- Required by ACF filter signature.
 * @return bool|string True if valid, error message if invalid.
 */
function golden_template_filter_cards_validate_subtitle( $valid, $value, $field, $input_name ) { // phpcs:ignore VariableAnalysis.CodeAnalysis.VariableAnalysis.UnusedVariable -- Required by ACF filter signature.
	// Only apply to filter card subtitle field.
	if ( ! isset( $field['key'] ) || 'field_golden_template_filter_card_subtitle' !== $field['key'] ) {
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
add_filter( 'acf/validate_value', 'golden_template_filter_cards_validate_subtitle', 10, 4 );

/**
 * Validate filter card tag character limit (max 50 characters)
 *
 * @param bool|string $valid Whether the field value is valid.
 * @param string      $value The field value.
 * @param array       $field The field array.
 * @param string      $input_name The input name (for repeater fields). // phpcs:ignore VariableAnalysis.CodeAnalysis.VariableAnalysis.UnusedVariable -- Required by ACF filter signature.
 * @return bool|string True if valid, error message if invalid.
 */
function golden_template_filter_cards_validate_tag( $valid, $value, $field, $input_name ) { // phpcs:ignore VariableAnalysis.CodeAnalysis.VariableAnalysis.UnusedVariable -- Required by ACF filter signature.
	// Only apply to filter card tag field.
	if ( ! isset( $field['key'] ) || 'field_golden_template_filter_card_tag' !== $field['key'] ) {
		return $valid;
	}

	// Count characters.
	$text_length = mb_strlen( $value );
	if ( $text_length > 50 ) {
		$valid = sprintf(
			/* translators: %d: Maximum character limit */
			__( 'Tag must not exceed 50 characters. Current length: %d characters.', 'golden-template' ),
			$text_length
		);
	}
	return $valid;
}
add_filter( 'acf/validate_value', 'golden_template_filter_cards_validate_tag', 10, 4 );
