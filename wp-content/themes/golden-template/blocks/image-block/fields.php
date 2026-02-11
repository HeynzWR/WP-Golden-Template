<?php
/**
 * Image Block - ACF Fields
 *
 * @package GoldenTemplate
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Register ACF fields for Image Block
 */
function golden_template_image_block_fields() {
	if ( ! function_exists( 'acf_add_local_field_group' ) ) {
		return;
	}

	acf_add_local_field_group(
		array(
			'key'    => 'group_golden_template_image_block',
			'title'  => 'Image Block',
			'fields' => array(
				// Content Tab
				array(
					'key'   => 'field_golden_template_image_block_content_tab',
					'label' => 'Content',
					'type'  => 'tab',
				),
				// Image Field
				array(
					'key'           => 'field_golden_template_image_block_image',
					'label'         => 'Image',
					'name'          => 'image',
					'type'          => 'image',
					'instructions'  => 'Required. Upload an image. Recommended size: 1100×650 px. Supported formats: JPG, PNG, WebP. Maximum file size: 2MB for optimal performance.',
					'required'      => 1,
					'return_format' => 'id',
					'preview_size'  => 'large',
					'library'       => 'all',
					'mime_types'    => 'jpg,jpeg,png,webp',
				),
				// Mobile Image Field
				array(
					'key'           => 'field_golden_template_image_block_mobile_image',
					'label'         => 'Mobile Image',
					'name'          => 'mobile_image',
					'type'          => 'image',
					'instructions'  => 'Optional: Upload a different image optimized for mobile devices. Recommended size: 390×240 px. If not provided, the desktop image will be used.',
					'required'      => 0,
					'return_format' => 'id',
					'preview_size'  => 'medium',
					'library'       => 'all',
					'mime_types'    => 'jpg,jpeg,png,webp',
				),
				// Accessibility Accordion
				array(
					'key'          => 'field_golden_template_image_block_image_accessibility_accordion',
					'label'        => 'Accessibility Settings',
					'type'         => 'accordion',
					'open'         => 0,
					'multi_expand' => 1,
					'endpoint'     => 0,
				),
				// Image Role
				array(
					'key'           => 'field_golden_template_image_block_image_role',
					'label'         => 'Image Role',
					'name'          => 'image_role',
					'type'          => 'radio',
					'instructions'  => 'Is this image decorative or does it convey important information? Decorative images are hidden from screen readers.',
					'choices'       => array(
						'decorative'    => 'Decorative (hidden from screen readers)',
						'informational' => 'Informational (needs description)',
					),
					'default_value' => 'informational',
					'layout'        => 'vertical',
				),
				// ALT Text
				array(
					'key'               => 'field_golden_template_image_block_image_alt',
					'label'             => 'ALT Text',
					'name'              => 'image_alt',
					'type'              => 'text',
					'instructions'      => 'Required when image role is informational. Describe what the image shows. Keep it concise and descriptive (maximum 200 characters). Use the fetch button (📥) to auto-populate from the media library.',
					'conditional_logic' => array(
						array(
							array(
								'field'    => 'field_golden_template_image_block_image_role',
								'operator' => '==',
								'value'    => 'informational',
							),
						),
					),
					'maxlength' => 200,
				),
				// Caption
				array(
					'key'          => 'field_golden_template_image_block_image_caption',
					'label'        => 'Caption',
					'name'         => 'image_caption',
					'type'         => 'text',
					'instructions' => 'Optional visible caption for the image (maximum 150 characters).',
					'maxlength'    => 150,
				),
				// Description
				array(
					'key'               => 'field_golden_template_image_block_image_description',
					'label'             => 'Description',
					'name'              => 'image_description',
					'type'              => 'textarea',
					'instructions'      => 'Optional. Detailed context for screen reader users. Provide additional information about the image that helps understand its purpose (maximum 300 characters).',
					'conditional_logic' => array(
						array(
							array(
								'field'    => 'field_golden_template_image_block_image_role',
								'operator' => '==',
								'value'    => 'informational',
							),
						),
					),
					'rows'      => 3,
					'maxlength' => 300,
				),
				// Close Accessibility Accordion
				array(
					'key'      => 'field_golden_template_image_block_image_accessibility_accordion_end',
					'label'    => 'Accessibility End',
					'type'     => 'accordion',
					'endpoint' => 1,
				),
			),
			'location' => array(
				array(
					array(
						'param'    => 'block',
						'operator' => '==',
						'value'    => 'acf/image-block',
					),
				),
			),
		)
	);
}
add_action( 'acf/init', 'golden_template_image_block_fields' );

/**
 * Validate image block file size (max 2MB)
 *
 * @param bool|string $valid Whether the field value is valid.
 * @param int         $value The attachment ID.
 * @param array       $field The field array.
 * @param string      $input_name The input name (for repeater fields). // phpcs:ignore VariableAnalysis.CodeAnalysis.VariableAnalysis.UnusedVariable -- Required by ACF filter signature.
 * @return bool|string True if valid, error message if invalid.
 */
function golden_template_image_block_validate_file_size( $valid, $value, $field, $input_name ) { // phpcs:ignore VariableAnalysis.CodeAnalysis.VariableAnalysis.UnusedVariable -- Required by ACF filter signature.
	// Only apply to image block image field.
	if ( ! isset( $field['key'] ) || 'field_golden_template_image_block_image' !== $field['key'] ) {
		return $valid;
	}

	// If no value, skip validation (required field will handle empty check).
	if ( empty( $value ) ) {
		return $valid;
	}

	// Get attachment file path.
	$file_path = get_attached_file( $value );
	if ( ! $file_path || ! file_exists( $file_path ) ) {
		return $valid;
	}

	// Get file size in bytes.
	$file_size = filesize( $file_path );
	$max_size  = 2 * 1024 * 1024; // 2MB in bytes.

	if ( $file_size > $max_size ) {
		$file_size_mb = round( $file_size / ( 1024 * 1024 ), 2 );
		$valid        = sprintf(
			/* translators: %1$s: Current file size in MB, %2$s: Maximum file size in MB */
			__( 'Image file size (%1$s MB) exceeds the maximum allowed size of %2$s MB. Please compress or resize the image before uploading.', 'golden-template' ),
			$file_size_mb,
			'2'
		);
	}

	return $valid;
}
add_filter( 'acf/validate_value', 'golden_template_image_block_validate_file_size', 10, 4 );
