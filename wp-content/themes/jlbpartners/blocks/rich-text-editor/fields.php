<?php
/**
 * Rich Text Editor Block - ACF Fields
 *
 * @package JLBPartners
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Register ACF fields for Rich Text Editor Block
 */
function jlbpartners_rich_text_editor_fields() {
	if ( ! function_exists( 'acf_add_local_field_group' ) ) {
		return;
	}

	acf_add_local_field_group(
		array(
			'key'    => 'group_rich_text_editor',
			'title'  => 'Rich Text Editor',
			'fields' => array(
				// CONTENT TAB
				array(
					'key'   => 'field_rich_text_editor_content_tab',
					'label' => 'Content',
					'type'  => 'tab',
				),

				// Rich Text Content
				array(
					'key'          => 'field_rich_text_editor_content',
					'label'        => 'Content',
					'name'         => 'content',
					'type'         => 'wysiwyg',
					'instructions' => 'Required. Add your rich text content. Use the editor toolbar to format text, add links, lists, and more.',
					'required'     => 1,
					'tabs'         => 'visual',
					'toolbar'      => 'standard',
					'media_upload' => 0,
				),

				// SETTINGS TAB
				array(
					'key'   => 'field_rich_text_editor_settings_tab',
					'label' => 'Settings',
					'type'  => 'tab',
				),

				// Show Top Line
				array(
					'key'           => 'field_rich_text_editor_show_top_line',
					'label'         => 'Show Top Line',
					'name'          => 'show_top_line',
					'type'          => 'true_false',
					'instructions'  => 'Optional. Display a decorative line above the content',
					'required'      => 0,
					'message'       => 'Display line above content',
					'default_value' => 0,
					'ui'            => 1,
					'ui_on_text'    => 'Yes',
					'ui_off_text'   => 'No',
				),

			// Show Bottom Line
			array(
				'key'           => 'field_rich_text_editor_show_bottom_line',
				'label'         => 'Show Bottom Line',
				'name'          => 'show_bottom_line',
				'type'          => 'true_false',
				'instructions'  => 'Optional. Display a decorative line below the content',
				'required'      => 0,
				'message'       => 'Display line below content',
				'default_value' => 0,
				'ui'            => 1,
				'ui_on_text'    => 'Yes',
				'ui_off_text'   => 'No',
			),
		),
			'location' => array(
				array(
					array(
						'param'    => 'block',
						'operator' => '==',
						'value'    => 'acf/rich-text-editor',
					),
				),
			),
		)
	);
}
add_action( 'acf/init', 'jlbpartners_rich_text_editor_fields' );
