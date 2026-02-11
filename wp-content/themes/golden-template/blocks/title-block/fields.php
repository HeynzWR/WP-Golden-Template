<?php
/**
 * Title Block Section - ACF Fields
 *
 * @package GoldenTemplate
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Register ACF fields for Title Block
 */
function golden_template_title_block_fields() {
	if ( ! function_exists( 'acf_add_local_field_group' ) ) {
		return;
	}

	acf_add_local_field_group(
		array(
			'key'                   => 'group_title_block',
			'title'                 => 'Title Block',
			'fields'                => array(
				// CONTENT TAB
				array(
					'key'   => 'field_title_block_content_tab',
					'label' => 'Content',
					'type'  => 'tab',
				),

				// Heading
				array(
					'key'               => 'field_title_block_heading',
					'label'             => 'Heading',
					'name'              => 'title_block_heading',
					'type'              => 'textarea',
					'instructions'      => 'Required. Main heading for the title block. Line breaks are allowed. Maximum 100 characters.',
					'required'          => 1,
					'maxlength'         => 100,
					'rows'              => 2,
					'placeholder'       => 'Enter heading...',
					'new_lines'         => 'br',
				),
				
				// Text/Paragraph
				array(
					'key'               => 'field_title_block_text',
					'label'             => 'Text',
					'name'              => 'title_block_text',
					'type'              => 'wysiwyg',
					'instructions'      => 'Optional. Description text for the title block. Supports bold text, italics, and links. Maximum 500 characters.',
					'required'          => 0,
					'tabs'              => 'visual',
					'toolbar'           => 'minimal',
					'media_upload'      => 0,
					'delay'             => 0,
				),
				
				// CTA & STYLE TAB
				array(
					'key'   => 'field_title_block_cta_style_tab',
					'label' => 'CTA & Style',
					'type'  => 'tab',
				),
				
				// Heading Font Size
				array(
					'key'           => 'field_title_block_button_style',
					'label'         => 'Heading Font Size',
					'name'          => 'title_block_button_style',
					'type'          => 'radio',
					'instructions'  => 'Choose the heading font size. Primary will give the heading a bigger font size in mobile view.',
					'choices'       => array(
						'primary' => 'Primary (Bigger Font Size)',
						'secondary' => 'Secondary',
					),
					'default_value' => 'primary',
					'layout'        => 'horizontal',
				),
				
				array(
					'key'               => 'field_title_block_btn',
					'label'             => 'Button',
					'name'              => 'title_block_btn',
					'type'              => 'link',
					'instructions'      => 'Optional. Call-to-action button with text and URL.',
					'required'          => 0,
					'return_format'     => 'array',
				),
			),
			'location'              => array(
				array(
					array(
						'param'    => 'block',
						'operator' => '==',
						'value'    => 'acf/title-block',
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
add_action( 'acf/init', 'golden_template_title_block_fields' );