<?php
/**
 * ACF Configuration
 *
 * @package GoldenTemplate
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Register flexible content field for components.
 */
function golden_template_register_acf_components() {
	if ( ! function_exists( 'acf_add_local_field_group' ) ) {
		return;
	}

	acf_add_local_field_group(
		array(
			'key'      => 'group_golden_template_components',
			'title'    => __( 'Page Components', 'golden-template' ),
			'fields'   => array(
				array(
					'key'        => 'field_golden_template_components',
					'label'      => __( 'Components', 'golden-template' ),
					'name'       => 'components',
					'type'       => 'flexible_content',
					'layouts'    => array(),
					'button_label' => __( 'Add Component', 'golden-template' ),
				),
			),

		)
	);
}
add_action( 'acf/init', 'golden_template_register_acf_components' );
