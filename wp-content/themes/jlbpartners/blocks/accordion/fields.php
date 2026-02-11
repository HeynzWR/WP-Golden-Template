<?php
/**
 * Accordion Block - ACF Fields
 *
 * @package JLBPartners
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Register ACF fields for Accordion Block
 */
function jlbpartners_accordion_fields() {
	if ( ! function_exists( 'acf_add_local_field_group' ) ) {
		return;
	}

	acf_add_local_field_group(
		array(
			'key'    => 'group_jlbpartners_accordion',
			'title'  => 'Accordion',
			'fields' => array(
				// Content Tab
				array(
					'key'   => 'field_jlbpartners_accordion_content_tab',
					'label' => 'Content',
					'type'  => 'tab',
				),
				array(
					'key'         => 'field_jlbpartners_accordion_items',
					'label'       => 'Accordion Items',
					'name'        => 'accordion_items',
					'type'        => 'repeater',
					'button_label' => 'Add Item',
					'required'    => 1,
					'min'         => 1,
					'layout'      => 'block',
					'collapsed'   => 'field_jlbpartners_accordion_title',
					'instructions' => 'Required. Add one or more accordion items. Each item requires a title and description.',
					'sub_fields'  => array(
						array(
							'key'      => 'field_jlbpartners_accordion_title',
							'label'    => 'Title',
							'name'     => 'title',
							'type'     => 'text',
							'required' => 1,
							'maxlength' => 50,
							'instructions' => 'Required. Maximum 50 characters.',
						),
						array(
							'key'   => 'field_jlbpartners_accordion_subtitle',
							'label' => 'Subtitle',
							'name'  => 'subtitle',
							'type'  => 'text',
							'maxlength' => 40,
							'instructions' => 'Optional. Displayed on the right side of the title (e.g., Job Role). Maximum 40 characters.',
						),
						array(
							'key'          => 'field_jlbpartners_accordion_content',
							'label'        => 'Description',
							'name'         => 'content',
							'type'         => 'wysiwyg',
							'required'     => 1,
							'instructions' => 'Required. Displayed when the accordion is opened. Supports bold text, links, and lists only. Maximum 500 characters.',
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
						'value'    => 'acf/accordion',
					),
				),
			),
		)
	);
}
add_action( 'acf/init', 'jlbpartners_accordion_fields' );

/**
 * Get allowed HTML tags for accordion description
 * Only allows: bold, links, and list tags
 *
 * @return array Allowed HTML tags.
 */
function jlbpartners_accordion_get_allowed_html() {
	return array(
		'strong' => array(),
		'b'       => array(),
		'a'       => array(
			'href'   => array(),
			'title'  => array(),
			'target' => array(),
			'rel'    => array(),
		),
		'ul'      => array(),
		'ol'      => array(),
		'li'      => array(),
		'p'       => array(), // Paragraphs are needed for proper formatting.
		'br'      => array(),
	);
}

/**
 * Restrict allowed HTML tags for accordion description field
 * Only allows: bold, links, and list tags
 *
 * @param string $value The field value.
 * @param int    $post_id The post ID.
 * @param array  $field The field array.
 * @return string Sanitized value.
 */
function jlbpartners_accordion_content_sanitize( $value, $post_id, $field ) {
	// Only apply to accordion content field (check by field key).
	if ( isset( $field['key'] ) && 'field_jlbpartners_accordion_content' === $field['key'] ) {
		return wp_kses( $value, jlbpartners_accordion_get_allowed_html() );
	}

	return $value;
}
add_filter( 'acf/update_value/type=wysiwyg', 'jlbpartners_accordion_content_sanitize', 20, 3 );
add_filter( 'acf/format_value/type=wysiwyg', 'jlbpartners_accordion_content_sanitize', 20, 3 );

/**
 * Validate accordion title character limit (max 50 characters)
 *
 * @param bool|string $valid Whether the field value is valid.
 * @param string      $value The field value.
 * @param array       $field The field array.
 * @param string      $input_name The input name (for repeater fields). // phpcs:ignore VariableAnalysis.CodeAnalysis.VariableAnalysis.UnusedVariable -- Required by ACF filter signature.
 * @return bool|string True if valid, error message if invalid.
 */
function jlbpartners_accordion_validate_title( $valid, $value, $field, $input_name ) { // phpcs:ignore VariableAnalysis.CodeAnalysis.VariableAnalysis.UnusedVariable -- Required by ACF filter signature.
	// Only apply to accordion title field.
	if ( ! isset( $field['key'] ) || 'field_jlbpartners_accordion_title' !== $field['key'] ) {
		return $valid;
	}

	// Count characters.
	$text_length = mb_strlen( $value );
	if ( $text_length > 50 ) {
		$valid = sprintf(
			/* translators: %d: Maximum character limit */
			__( 'Title must not exceed 50 characters. Current length: %d characters.', 'jlbpartners' ),
			$text_length
		);
	}
	return $valid;
}
add_filter( 'acf/validate_value', 'jlbpartners_accordion_validate_title', 10, 4 );

/**
 * Validate accordion subtitle character limit (max 40 characters)
 *
 * @param bool|string $valid Whether the field value is valid.
 * @param string      $value The field value.
 * @param array       $field The field array.
 * @param string      $input_name The input name (for repeater fields). // phpcs:ignore VariableAnalysis.CodeAnalysis.VariableAnalysis.UnusedVariable -- Required by ACF filter signature.
 * @return bool|string True if valid, error message if invalid.
 */
function jlbpartners_accordion_validate_subtitle( $valid, $value, $field, $input_name ) { // phpcs:ignore VariableAnalysis.CodeAnalysis.VariableAnalysis.UnusedVariable -- Required by ACF filter signature.
	// Only apply to accordion subtitle field.
	if ( ! isset( $field['key'] ) || 'field_jlbpartners_accordion_subtitle' !== $field['key'] ) {
		return $valid;
	}

	// Count characters.
	$text_length = mb_strlen( $value );
	if ( $text_length > 40 ) {
		$valid = sprintf(
			/* translators: %d: Maximum character limit */
			__( 'Subtitle must not exceed 40 characters. Current length: %d characters.', 'jlbpartners' ),
			$text_length
		);
	}
	return $valid;
}
add_filter( 'acf/validate_value', 'jlbpartners_accordion_validate_subtitle', 10, 4 );
