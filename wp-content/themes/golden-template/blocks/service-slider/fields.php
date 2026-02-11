<?php
/**
 * Service Slider Block - ACF Fields
 *
 * @package GoldenTemplate
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Register ACF fields for Service Slider Block
 */
function golden_template_service_slider_fields() {
	if ( ! function_exists( 'acf_add_local_field_group' ) ) {
		return;
	}

	acf_add_local_field_group(
		array(
			'key'    => 'group_service_slider',
			'title'  => 'Service Slider',
			'fields' => array(
				// CONTENT TAB
				array(
					'key'   => 'field_service_slider_content_tab',
					'label' => 'Content',
					'type'  => 'tab',
				),

				// Service Slider Title
				array(
					'key'          => 'field_service_slider_title',
					'label'        => 'Service Slider Title',
					'name'         => 'service_slider_title',
					'type'         => 'text',
					'instructions' => 'Optional. Enter the main title for the service slider section. Keep it concise and descriptive (maximum 100 characters).',
					'required'     => 0,
					'maxlength'    => 100,
					'placeholder'  => 'Our Services',
				),

				// Service Slider Description
				array(
					'key'          => 'field_service_slider_description',
					'label'        => 'Service Slider Description',
					'name'         => 'service_slider_description',
					'type'         => 'wysiwyg',
					'instructions' => 'Optional description of the services. Keep it informative and concise(maximum 500 characters).',
					'required'     => 0,
					'tabs'         => 'visual',
					'toolbar'      => 'minimal',
					'media_upload' => 0,
					'delay'        => 0,
				),

				// SERVICES TAB
				array(
					'key'   => 'field_service_slider_services_tab',
					'label' => 'Services',
					'type'  => 'tab',
				),

				// Service Items Repeater
				array(
					'key'          => 'field_service_items',
					'label'        => 'Service Items',
					'name'         => 'service_items',
					'type'         => 'repeater',
					'instructions' => 'Required. Add exactly 3 service items with image, title, and description. Each service item requires an image, title, and description.',
					'required'     => 1,
					'layout'       => 'block',
					'button_label' => 'Add Service',
					'min'          => 3,
					'max'          => 3,
					'collapsed'    => 'field_service_title',
					'sub_fields'   => array(
						// Service Image
						array(
							'key'           => 'field_service_image',
							'label'         => 'Service Image',
							'name'          => 'service_image',
							'type'          => 'image',
							'instructions'  => 'Required. Upload an image for this service. Recommended size: 800×1200 px. Supported formats: JPG, PNG, WebP. Maximum file size: 5MB for optimal performance.',
							'required'      => 1,
							'return_format' => 'id',
							'preview_size'  => 'medium',
							'library'       => 'all',
							'mime_types'    => 'jpg,jpeg,png,webp',
						),

						// Mobile Service Image
						array(
							'key'           => 'field_service_mobile_image',
							'label'         => 'Mobile Image',
							'name'          => 'service_mobile_image',
							'type'          => 'image',
							'instructions'  => 'Optional: Upload a different image optimized for mobile devices. Recommended size: 320×400 px. If not provided, the desktop image will be used.',
							'required'      => 0,
							'return_format' => 'id',
							'preview_size'  => 'medium',
							'library'       => 'all',
							'mime_types'    => 'jpg,jpeg,png,webp',
						),

						// Image Accessibility Accordion
						array(
							'key'          => 'field_service_image_accessibility_accordion',
							'label'        => 'Accessibility Settings',
							'type'         => 'accordion',
							'open'         => 0,
							'multi_expand' => 1,
							'endpoint'     => 0,
						),

						// Image Role
						array(
							'key'           => 'field_service_image_role',
							'label'         => 'Image Role',
							'name'          => 'service_image_role',
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
							'key'               => 'field_service_image_alt',
							'label'             => 'ALT Text',
							'name'              => 'service_image_alt',
							'type'              => 'text',
							'instructions'      => 'Required when image role is informational. Describe what the image shows. Keep it concise and descriptive (maximum 200 characters). Use the fetch button (📥) to auto-populate from the media library.',
							'conditional_logic' => array(
								array(
									array(
										'field'    => 'field_service_image_role',
										'operator' => '==',
										'value'    => 'informational',
									),
								),
							),
							'maxlength' => 200,
						),

						// Image Caption
						array(
							'key'          => 'field_service_image_caption',
							'label'        => 'Caption',
							'name'         => 'service_image_caption',
							'type'         => 'text',
							'instructions' => 'Optional visible caption for the image (maximum 150 characters).',
							'maxlength'    => 150,
						),

						// Image Description
						array(
							'key'               => 'field_service_image_description',
							'label'             => 'Description',
							'name'              => 'service_image_description',
							'type'              => 'textarea',
							'instructions'      => 'Optional. Detailed context for screen reader users. Provide additional information about the image that helps understand its purpose (maximum 300 characters).',
							'conditional_logic' => array(
								array(
									array(
										'field'    => 'field_service_image_role',
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
							'key'      => 'field_service_image_accessibility_accordion_end',
							'label'    => 'Accessibility End',
							'type'     => 'accordion',
							'endpoint' => 1,
						),

						// Service Title
						array(
							'key'          => 'field_service_title',
							'label'        => 'Service Title',
							'name'         => 'service_title',
							'type'         => 'text',
							'instructions' => 'Required. Enter the service title. Keep it concise and clear (maximum 100 characters).',
							'required'     => 1,
							'maxlength'    => 100,
							'placeholder'  => 'Development',
						),

						// Service Description
						array(
							'key'          => 'field_service_description',
							'label'        => 'Service Description',
							'name'         => 'service_description',
							'type'         => 'wysiwyg',
							'instructions' => 'Required. Enter a brief description of the service. Keep it informative and concise(maximum 200 characters).',
							'required'     => 1,
							'tabs'         => 'visual',
							'toolbar'      => 'minimal',
							'media_upload' => 0,
							'delay'        => 0,
						),
					),
				),

				// CTA & STYLE TAB
				array(
					'key'   => 'field_service_slider_cta_style_tab',
					'label' => 'CTA & Style',
					'type'  => 'tab',
				),

				// Link
				array(
					'key'           => 'field_learn_more_link',
					'label'         => 'Link',
					'name'          => 'learn_more_link',
					'type'          => 'link',
					'instructions'  => 'Add an optional link for the Learn More button. The link text will be used as the button label.',
					'required'      => 0,
					'return_format' => 'array',
				),

				// Remove Padding Toggle
				array(
					'key'          => 'field_remove_padding',
					'label'        => 'Remove bottom padding',
					'name'         => 'remove_padding',
					'type'         => 'true_false',
					'instructions' => 'Enable this option to remove padding from the service section.',
					'required'     => 0,
					'default_value' => 0,
					'ui'           => 1,
					'ui_on_text'   => 'Enabled',
					'ui_off_text'  => 'Disabled',
				),
			),
			'location' => array(
				array(
					array(
						'param'    => 'block',
						'operator' => '==',
						'value'    => 'acf/service-slider',
					),
				),
			),
		)
	);
}
add_action( 'acf/init', 'golden_template_service_slider_fields' );
