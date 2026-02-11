<?php
/**
 * About Section Block - ACF Fields
 *
 * @package JLBPartners
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Register ACF fields for About Section Block
 */
function jlbpartners_about_section_fields() {
	if ( ! function_exists( 'acf_add_local_field_group' ) ) {
		return;
	}

	acf_add_local_field_group(
		array(
			'key'    => 'group_about_section',
			'title'  => 'About Section',
			'fields' => array(
				// CONTENT TAB
				array(
					'key'   => 'field_about_section_content_tab',
					'label' => 'Content',
					'type'  => 'tab',
				),

				// Title
				array(
					'key'          => 'field_about_title',
					'label'        => 'Section Title',
					'name'         => 'about_title',
					'type'         => 'text',
					'instructions' => 'Optional. Enter the main heading for the about section. Keep it concise and descriptive (maximum 100 characters).',
					'required'     => 0,
					'maxlength'    => 100,
					'placeholder'  => 'About us',
					'default_value' => '',
				),

				// Stats Repeater
				array(
					'key'          => 'field_about_stats',
					'label'        => 'Statistics',
					'name'         => 'about_stats',
					'type'         => 'repeater',
					'instructions' => 'Required. Add exactly 3 statistics to display in the about section. Each statistic requires a value and label.',
					'required'     => 1,
					'layout'       => 'table',
					'button_label' => 'Add Statistic',
					'min'          => 3,
					'max'          => 3,
					'sub_fields'   => array(
						array(
							'key'          => 'field_stat_value',
							'label'        => 'Stat Value',
							'name'         => 'stat_value',
							'type'         => 'text',
							'instructions' => 'Required. The number or value (e.g., 60+, $4 Billion+). Keep it concise (maximum 50 characters).',
							'required'     => 1,
							'maxlength'    => 50,
							'placeholder'  => 'e.g., 60+',
						),
						array(
							'key'          => 'field_stat_label',
							'label'        => 'Stat Label',
							'name'         => 'stat_label',
							'type'         => 'text',
							'instructions' => 'Required. Description of the statistic. Keep it concise (maximum 100 characters).',
							'required'     => 1,
							'maxlength'    => 100,
							'placeholder'  => 'e.g., Assets developed',
						),
					),
				),

				// Content Text
				array(
					'key'          => 'field_about_text',
					'label'        => 'Content',
					'name'         => 'about_text',
					'type'         => 'wysiwyg',
					'instructions' => 'Optional. Enter the description text for the about section. Keep it informative and engaging (maximum 1000 characters).',
					'required'     => 0,
					'tabs'         => 'visual',
					'toolbar'      => 'minimal',
					'media_upload' => 0,
					'delay'        => 0,
				),

				// CTA Button Link
				array(
					'key'           => 'field_about_btn',
					'label'         => 'CTA Button',
					'name'          => 'about_btn',
					'type'          => 'link',
					'instructions'  => 'Add an optional call-to-action button. The link text will be used as the button label.',
					'required'      => 0,
					'return_format' => 'array',
				),

				// MEDIA TAB
				array(
					'key'   => 'field_about_section_media_tab',
					'label' => 'Media',
					'type'  => 'tab',
				),

				// Image
				array(
					'key'           => 'field_about_image',
					'label'         => 'About Image',
					'name'          => 'about_image',
					'type'          => 'image',
					'instructions'  => 'Optional. Upload an image for the about section. Recommended size: 800×900 px. Supported formats: JPG, PNG, WebP. Maximum file size: 5MB for optimal performance.',
					'required'      => 0,
					'return_format' => 'id',
					'preview_size'  => 'large',
					'library'       => 'all',
				),

				// Mobile Image
				array(
					'key'           => 'field_about_mobile_image',
					'label'         => 'Mobile Image',
					'name'          => 'about_mobile_image',
					'type'          => 'image',
					'instructions'  => 'Optional: Upload a different image optimized for mobile devices. Recommended size: 393×524 px. If not provided, the desktop image will be used.',
					'required'      => 0,
					'return_format' => 'id',
					'preview_size'  => 'medium',
					'library'       => 'all',
				),

				// Image Accessibility Accordion
				array(
					'key'          => 'field_about_image_accessibility_accordion',
					'label'        => 'Accessibility Settings',
					'type'         => 'accordion',
					'open'         => 0,
					'multi_expand' => 1,
					'endpoint'     => 0,
				),

				// Image Role
				array(
					'key'           => 'field_about_image_role',
					'label'         => 'Image Role',
					'name'          => 'about_image_role',
					'type'          => 'radio',
					'instructions'  => 'Is this image decorative or does it convey important information? Decorative images are hidden from screen readers.',
					'choices'       => array(
						'decorative'    => 'Decorative (hidden from screen readers)',
						'informational' => 'Informational (needs description)',
					),
					'default_value' => 'informational',
					'layout'        => 'vertical',
				),

				// Image ALT Text
				array(
					'key'               => 'field_about_image_alt',
					'label'             => 'ALT Text',
					'name'              => 'about_image_alt',
					'type'              => 'text',
					'instructions'      => 'Required when image role is informational. Describe what the image shows. Keep it concise and descriptive (maximum 200 characters). Use the fetch button (📥) to auto-populate from the media library.',
					'conditional_logic' => array(
						array(
							array(
								'field'    => 'field_about_image_role',
								'operator' => '==',
								'value'    => 'informational',
							),
						),
					),
					'maxlength' => 200,
				),

				// Image Caption
				array(
					'key'          => 'field_about_image_caption',
					'label'        => 'Caption',
					'name'         => 'about_image_caption',
					'type'         => 'text',
					'instructions' => 'Optional visible caption for the image (maximum 150 characters).',
					'maxlength'    => 150,
				),

				// Image Description
				array(
					'key'               => 'field_about_image_description',
					'label'             => 'Description',
					'name'              => 'about_image_description',
					'type'              => 'textarea',
					'instructions'      => 'Optional. Detailed context for screen reader users. Provide additional information about the image that helps understand its purpose (maximum 300 characters).',
					'conditional_logic' => array(
						array(
							array(
								'field'    => 'field_about_image_role',
								'operator' => '==',
								'value'    => 'informational',
							),
						),
					),
					'rows'      => 3,
					'maxlength' => 300,
				),

				// Close Image Accessibility Accordion
				array(
					'key'      => 'field_about_image_accessibility_accordion_end',
					'label'    => 'Accessibility End',
					'type'     => 'accordion',
					'endpoint' => 1,
				),

				// SETTINGS TAB
				array(
					'key'   => 'field_about_section_settings_tab',
					'label' => 'Settings',
					'type'  => 'tab',
				),

				// Content and Image Alignment
				array(
					'key'           => 'field_about_content_image_alignment',
					'label'         => 'Content and image alignment',
					'name'          => 'about_content_image_alignment',
					'type'          => 'radio',
					'instructions'  => 'Optional. Choose the layout alignment for content and image.',
					'choices'       => array(
						'content_left'  => 'Content left / Image right',
						'image_left'    => 'Image left / Content right',
					),
					'default_value' => 'content_left',
					'layout'        => 'horizontal',
				),
			),
			'location' => array(
				array(
					array(
						'param'    => 'block',
						'operator' => '==',
						'value'    => 'acf/about-section',
					),
				),
			),
		)
	);
}
add_action( 'acf/init', 'jlbpartners_about_section_fields' );
