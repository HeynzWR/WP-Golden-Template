<?php
/**
 * ACF Configuration
 *
 * @package JLBPartners
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Register flexible content field for components.
 */
function jlbpartners_register_acf_components() {
	if ( ! function_exists( 'acf_add_local_field_group' ) ) {
		return;
	}

	acf_add_local_field_group(
		array(
			'key'      => 'group_jlbpartners_components',
			'title'    => __( 'Page Components', 'jlbpartners' ),
			'fields'   => array(
				array(
					'key'        => 'field_jlbpartners_components',
					'label'      => __( 'Components', 'jlbpartners' ),
					'name'       => 'components',
					'type'       => 'flexible_content',
					'layouts'    => array(),
					'button_label' => __( 'Add Component', 'jlbpartners' ),
				),
			),

		)
	);
}
add_action( 'acf/init', 'jlbpartners_register_acf_components' );
