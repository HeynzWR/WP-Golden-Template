<?php
/**
 * Milestones Block - ACF Fields
 *
 * @package JLBPartners
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Register ACF fields for Milestones Block
 */
function jlbpartners_milestones_fields() {
	if ( ! function_exists( 'acf_add_local_field_group' ) ) {
		return;
	}

	acf_add_local_field_group(
		array(
			'key'    => 'group_jlbpartners_milestones',
			'title'  => 'Milestones',
			'fields' => array(
				array(
					'key'          => 'field_jlbpartners_milestones_items',
					'label'        => 'Milestone Items',
					'name'         => 'milestone_items',
					'type'         => 'repeater',
					'button_label' => 'Add Milestone',
					'required'     => 1,
					'min'          => 3,
					'layout'       => 'block',
					'collapsed'    => 'field_jlbpartners_milestone_label',
					'instructions' => 'Required. Add timeline items. Only the first item will be active by default. Minimum 3 items required.',
					'sub_fields'   => array(
						array(
							'key'      => 'field_jlbpartners_milestone_label',
							'label'    => 'Label / Year',
							'name'     => 'label',
							'type'     => 'text',
							'required' => 1,
							'maxlength' => 4,
							'instructions' => 'Required. Primary identifier for the timeline item. Can be numeric or short text (e.g., 1980, Q1). Maximum 4 characters.',
						),
						array(
							'key'          => 'field_jlbpartners_milestone_title',
							'label'        => 'Title / Headline',
							'name'         => 'title',
							'type'         => 'wysiwyg',
							'required'     => 1,
							'instructions' => 'Required. Title or headline for this milestone. Supports paragraphs, headings, bold text, links, and lists. Maximum 100 characters.',
							'tabs'         => 'visual',
							'toolbar'      => 'format',
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
						'value'    => 'acf/milestones',
					),
				),
			),
		)
	);
}
add_action( 'acf/init', 'jlbpartners_milestones_fields' );

/**
 * Validate milestone label character limit (max 4 characters)
 *
 * @param bool|string $valid Whether the field value is valid.
 * @param string      $value The field value.
 * @param array       $field The field array.
 * @param string      $input_name The input name (for repeater fields). // phpcs:ignore VariableAnalysis.CodeAnalysis.VariableAnalysis.UnusedVariable -- Required by ACF filter signature.
 * @return bool|string True if valid, error message if invalid.
 */
function jlbpartners_milestones_validate_label( $valid, $value, $field, $input_name ) { // phpcs:ignore VariableAnalysis.CodeAnalysis.VariableAnalysis.UnusedVariable -- Required by ACF filter signature.
	// Only apply to milestone label field.
	if ( isset( $field['key'] ) && 'field_jlbpartners_milestone_label' === $field['key'] ) {
		// Count characters.
		$text_length = mb_strlen( $value );
		if ( $text_length > 4 ) {
			$valid = sprintf(
				/* translators: %d: Maximum character limit */
				__( 'Label / Year must not exceed 4 characters. Current length: %d characters.', 'jlbpartners' ),
				$text_length
			);
		}
	}
	return $valid;
}
add_filter( 'acf/validate_value', 'jlbpartners_milestones_validate_label', 10, 4 );
