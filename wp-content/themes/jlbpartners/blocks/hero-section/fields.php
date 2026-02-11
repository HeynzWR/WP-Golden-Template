<?php
/**
 * Hero Section Block - ACF Fields
 *
 * @package JLBPartners
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Register ACF fields for Hero Section Block
 */
function jlbpartners_hero_section_fields() {
	if ( ! function_exists( 'acf_add_local_field_group' ) ) {
		return;
	}

	acf_add_local_field_group(
		array(
			'key'    => 'group_hero_section',
			'title'  => 'Hero Section',
			'fields' => array(
				// CONTENT TAB
				array(
					'key'   => 'field_hero_section_content_tab',
					'label' => 'Content',
					'type'  => 'tab',
				),

				// Heading Level
				array(
					'key'           => 'field_hero_section_heading_level',
					'label'         => 'Select heading level',
					'name'          => 'hero_heading_level',
					'type'          => 'select',
					'instructions'  => 'Optional. Choose the HTML heading level for the title. H1 is typically used for the main page heading, while H2 is used for section headings.',
					'choices'       => array(
						'h1' => 'H1',
						'h2' => 'H2',
					),
					'default_value' => 'h1',
					'allow_null'    => 0,
					'multiple'      => 0,
				),

				// Title
				array(
					'key'          => 'field_hero_section_title',
					'label'        => 'Title',
					'name'         => 'hero_title',
					'type'         => 'text',
					'instructions' => 'Required.Enter the main headline for the hero section. Keep it concise and impactful (maximum 100 characters).',
					'required'     => 1,
					'maxlength'    => 100,
					'placeholder'  => 'Enter your hero headline',
				),

				// MEDIA TAB
				array(
					'key'   => 'field_hero_section_media_tab',
					'label' => 'Media',
					'type'  => 'tab',
				),

				// Background Type
				array(
					'key'           => 'field_hero_section_background_type',
					'label'         => 'Background Type',
					'name'          => 'background_type',
					'type'          => 'radio',
					'instructions'  => 'Choose whether to use an image or video as the background. Image is recommended for better performance. If no file is added, a placeholder image will be displayed.',
					'choices'       => array(
						'image' => 'Image',
						'video' => 'Video',
					),
					'default_value' => 'image',
					'layout'        => 'horizontal',
				),

				// Background Image
				array(
					'key'               => 'field_hero_section_background_image',
					'label'             => 'Background Image',
					'name'              => 'background_image',
					'type'              => 'image',
					'instructions'      => 'Optional. Upload a high-quality background image. Recommended size: 1700×1000 px. Supported formats: JPG, PNG, WebP. Maximum file size: 5MB for optimal performance.',
					'required'          => 0,
					'return_format'     => 'id',
					'preview_size'      => 'large',
					'conditional_logic' => array(
						array(
							array(
								'field'    => 'field_hero_section_background_type',
								'operator' => '==',
								'value'    => 'image',
							),
						),
					),
				),

				// Mobile Background Image
				array(
					'key'               => 'field_hero_section_mobile_background_image',
					'label'             => 'Mobile Background Image',
					'name'              => 'mobile_background_image',
					'type'              => 'image',
					'instructions'      => 'Optional: Upload a different background image optimized for mobile devices. Recommended size: 393×766 px. If not provided, the desktop image will be used.',
					'required'          => 0,
					'return_format'     => 'id',
					'preview_size'      => 'medium',
					'conditional_logic' => array(
						array(
							array(
								'field'    => 'field_hero_section_background_type',
								'operator' => '==',
								'value'    => 'image',
							),
						),
					),
				),

				// Background Image Accessibility Accordion
				array(
					'key'          => 'field_hero_section_background_image_accessibility_accordion',
					'label'        => 'Accessibility Settings',
					'type'         => 'accordion',
					'open'         => 0,
					'multi_expand' => 1,
					'endpoint'     => 0,
					'conditional_logic' => array(
						array(
							array(
								'field'    => 'field_hero_section_background_type',
								'operator' => '==',
								'value'    => 'image',
							),
						),
					),
				),

				// Background Image Role
				array(
					'key'           => 'field_hero_section_background_image_role',
					'label'         => 'Image Role',
					'name'          => 'background_image_role',
					'type'          => 'radio',
					'instructions'  => 'Is this background image decorative or does it convey important information? Decorative images are hidden from screen readers.',
					'choices'       => array(
						'decorative'    => 'Decorative (hidden from screen readers)',
						'informational' => 'Informational (needs description)',
					),
					'default_value' => 'decorative',
					'layout'        => 'vertical',
					'conditional_logic' => array(
						array(
							array(
								'field'    => 'field_hero_section_background_type',
								'operator' => '==',
								'value'    => 'image',
							),
						),
					),
				),

				// Background Image ALT Text
				array(
					'key'               => 'field_hero_section_background_image_alt',
					'label'             => 'ALT Text',
					'name'              => 'background_image_alt',
					'type'              => 'text',
					'instructions'      => 'Required when image role is informational. Describe what the background image shows. Keep it concise and descriptive (maximum 200 characters). Use the fetch button (📥) to auto-populate from the media library.',
					'conditional_logic' => array(
						array(
							array(
								'field'    => 'field_hero_section_background_type',
								'operator' => '==',
								'value'    => 'image',
							),
							array(
								'field'    => 'field_hero_section_background_image_role',
								'operator' => '==',
								'value'    => 'informational',
							),
						),
					),
					'maxlength' => 200,
				),

				// Background Image Caption
				array(
					'key'          => 'field_hero_section_background_image_caption',
					'label'        => 'Caption',
					'name'         => 'background_image_caption',
					'type'         => 'text',
					'instructions' => 'Optional visible caption for the background image (maximum 150 characters).',
					'maxlength'    => 150,
					'conditional_logic' => array(
						array(
							array(
								'field'    => 'field_hero_section_background_type',
								'operator' => '==',
								'value'    => 'image',
							),
						),
					),
				),

				// Background Image Description
				array(
					'key'               => 'field_hero_section_background_image_description',
					'label'             => 'Description',
					'name'              => 'background_image_description',
					'type'              => 'textarea',
					'instructions'      => 'Optional. Detailed context for screen reader users. Provide additional information about the image that helps understand its purpose (maximum 300 characters).',
					'conditional_logic' => array(
						array(
							array(
								'field'    => 'field_hero_section_background_type',
								'operator' => '==',
								'value'    => 'image',
							),
							array(
								'field'    => 'field_hero_section_background_image_role',
								'operator' => '==',
								'value'    => 'informational',
							),
						),
					),
					'rows'      => 3,
					'maxlength' => 300,
				),

				// Close Background Image Accessibility Accordion
				array(
					'key'      => 'field_hero_section_background_image_accessibility_accordion_end',
					'label'    => 'Accessibility End',
					'type'     => 'accordion',
					'endpoint' => 1,
					'conditional_logic' => array(
						array(
							array(
								'field'    => 'field_hero_section_background_type',
								'operator' => '==',
								'value'    => 'image',
							),
						),
					),
				),

				// Background Video
				array(
					'key'               => 'field_hero_section_background_video',
					'label'             => 'Background Video',
					'name'              => 'background_video',
					'type'              => 'file',
					'instructions'      => 'Required when background type is video. Upload a background video file. Recommended format: MP4 (H.264 codec). Maximum file size: 5MB for optimal performance. Video should be muted and loop seamlessly.',
					'required'          => 1,
					'return_format'     => 'array',
					'mime_types'        => 'mp4,webm',
					'conditional_logic' => array(
						array(
							array(
								'field'    => 'field_hero_section_background_type',
								'operator' => '==',
								'value'    => 'video',
							),
						),
					),
				),

				// Video Accessibility Accordion
				array(
					'key'          => 'field_hero_section_background_video_accessibility_accordion',
					'label'        => 'Accessibility Settings',
					'type'         => 'accordion',
					'open'         => 0,
					'multi_expand' => 1,
					'endpoint'     => 0,
					'conditional_logic' => array(
						array(
							array(
								'field'    => 'field_hero_section_background_type',
								'operator' => '==',
								'value'    => 'video',
							),
						),
					),
				),

				// Video Title
				array(
					'key'          => 'field_hero_section_background_video_title',
					'label'        => 'Video Title',
					'name'         => 'background_video_title',
					'type'         => 'text',
					'instructions' => 'Optional. Descriptive title for the video (maximum 100 characters). Use the fetch button (📥) to auto-populate from the media library.',
					'maxlength'    => 100,
					'conditional_logic' => array(
						array(
							array(
								'field'    => 'field_hero_section_background_type',
								'operator' => '==',
								'value'    => 'video',
							),
						),
					),
				),

				// Video Description
				array(
					'key'          => 'field_hero_section_background_video_description',
					'label'        => 'Video Description',
					'name'         => 'background_video_description',
					'type'         => 'textarea',
					'instructions' => 'Optional. Describe what happens in the video for screen reader users (maximum 300 characters).',
					'rows'         => 3,
					'maxlength'    => 300,
					'conditional_logic' => array(
						array(
							array(
								'field'    => 'field_hero_section_background_type',
								'operator' => '==',
								'value'    => 'video',
							),
						),
					),
				),

				// Video Transcript URL
				array(
					'key'          => 'field_hero_section_background_video_transcript_url',
					'label'        => 'Transcript URL',
					'name'         => 'background_video_transcript_url',
					'type'         => 'url',
					'instructions' => 'Optional. Link to a text transcript of the video (WCAG requirement for accessibility).',
					'conditional_logic' => array(
						array(
							array(
								'field'    => 'field_hero_section_background_type',
								'operator' => '==',
								'value'    => 'video',
							),
						),
					),
				),

				// Close Video Accessibility Accordion
				array(
					'key'      => 'field_hero_section_background_video_accessibility_accordion_end',
					'label'    => 'Accessibility End',
					'type'     => 'accordion',
					'endpoint' => 1,
					'conditional_logic' => array(
						array(
							array(
								'field'    => 'field_hero_section_background_type',
								'operator' => '==',
								'value'    => 'video',
							),
						),
					),
				),

				// Video Poster Image Accordion
				array(
					'key'          => 'field_hero_section_background_video_poster_accordion',
					'label'        => 'Poster Image',
					'type'         => 'accordion',
					'open'         => 0,
					'multi_expand' => 1,
					'endpoint'     => 0,
					'conditional_logic' => array(
						array(
							array(
								'field'    => 'field_hero_section_background_type',
								'operator' => '==',
								'value'    => 'video',
							),
						),
					),
				),

				// Video Poster Image
				array(
					'key'           => 'field_hero_section_background_video_poster',
					'label'         => 'Fallback Image',
					'name'          => 'background_video_poster',
					'type'          => 'image',
					'instructions'  => 'Optional. Upload a poster image displayed before the video loads and for users with reduced motion preferences. Recommended size: 1920x1080px.',
					'return_format' => 'id',
					'preview_size'  => 'large',
					'conditional_logic' => array(
						array(
							array(
								'field'    => 'field_hero_section_background_type',
								'operator' => '==',
								'value'    => 'video',
							),
						),
					),
				),

				// Close Video Poster Accordion
				array(
					'key'      => 'field_hero_section_background_video_poster_accordion_end',
					'label'    => 'Poster End',
					'type'     => 'accordion',
					'endpoint' => 1,
					'conditional_logic' => array(
						array(
							array(
								'field'    => 'field_hero_section_background_type',
								'operator' => '==',
								'value'    => 'video',
							),
						),
					),
				),

				// CTA TAB
				array(
					'key'   => 'field_hero_section_cta_tab',
					'label' => 'CTA',
					'type'  => 'tab',
				),

				// CTA Link
				array(
					'key'          => 'field_hero_section_cta',
					'label'        => 'Call to Action',
					'name'         => 'hero_cta_link',
					'type'         => 'link',
					'instructions' => 'Optional. Add a button or link. The link text will be used as the button label.',
					'return_format' => 'array',
				),

				// CTA Button Size
				array(
					'key'           => 'field_hero_section_cta_size',
					'label'         => 'CTA Button Size',
					'name'          => 'hero_cta_size',
					'type'          => 'radio',
					'instructions'  => 'Optional. Choose the size of the call-to-action button. Large is recommended for hero sections.',
					'choices'       => array(
						'large' => 'Large',
						'small' => 'Small',
					),
					'default_value' => 'large',
					'layout'        => 'horizontal',
				),
			),
			'location' => array(
				array(
					array(
						'param'    => 'block',
						'operator' => '==',
						'value'    => 'acf/hero-section',
					),
				),
			),
		)
	);
}
add_action( 'acf/init', 'jlbpartners_hero_section_fields' );
