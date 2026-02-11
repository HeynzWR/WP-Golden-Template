<?php
/**
 * Line Block - ACF Fields
 *
 * @package JLBPartners
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Register ACF fields for Line Block
 */
function jlbpartners_line_block_fields() {
	if ( ! function_exists( 'acf_add_local_field_group' ) ) {
		return;
	}

	acf_add_local_field_group(
		array(
			'key'    => 'group_line_block',
			'title'  => 'Line Block',
			'fields' => array(
				// CONTENT TAB
				array(
					'key'   => 'field_line_block_content_tab',
					'label' => 'Content',
					'type'  => 'tab',
				),

				// Text
				array(
					'key'          => 'field_line_block_text',
					'label'        => 'Text',
					'name'         => 'text',
					'type'         => 'wysiwyg',
					'instructions' => 'Optional. Enter the text content for the line block. You can format text using bold, italic, and links. Keep it concise and impactful.',
					'required'     => 0,
					'tabs'         => 'visual',
					'toolbar'      => 'minimal',
					'media_upload' => 0,
				),

				// SETTINGS TAB
				array(
					'key'   => 'field_line_block_settings_tab',
					'label' => 'Settings',
					'type'  => 'tab',
				),

				// Show Line After Text
				array(
					'key'          => 'field_line_block_show_line',
					'label'        => 'Show Line After Text',
					'name'         => 'show_line',
					'type'         => 'true_false',
					'instructions' => 'Optional. Turn this ON to display a vertical line after the text content. The line appears below the text and extends downward.',
					'required'     => 0,
					'message'      => 'Display line after text',
					'default_value' => 1,
					'ui'           => 1,
					'ui_on_text'   => 'Yes',
					'ui_off_text'  => 'No',
				),
			),
			'location' => array(
				array(
					array(
						'param'    => 'block',
						'operator' => '==',
						'value'    => 'acf/line-block',
					),
				),
			),
		)
	);
}
add_action( 'acf/init', 'jlbpartners_line_block_fields' );
