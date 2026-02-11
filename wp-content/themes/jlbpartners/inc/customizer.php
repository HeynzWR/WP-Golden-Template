<?php
/**
 * Customizer settings for header CTA
 *
 * @package JLBPartners
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Register customizer settings
 */
function jlbpartners_customize_register( $wp_customize ) {
	// Add Header section
	$wp_customize->add_section(
		'jlbpartners_header_section',
		array(
			'title'    => __( 'Header Settings', 'jlbpartners' ),
			'priority' => 30,
		)
	);

	// Header CTA URL setting
	$wp_customize->add_setting(
		'jlbpartners_header_cta_url',
		array(
			'default'           => '#',
			'sanitize_callback' => 'esc_url_raw',
			'transport'         => 'refresh',
		)
	);

	// Header CTA URL control
	$wp_customize->add_control(
		'jlbpartners_header_cta_url',
		array(
			'label'       => __( 'Header CTA URL', 'jlbpartners' ),
			'description' => __( 'Enter the URL for the header call-to-action button.', 'jlbpartners' ),
			'section'     => 'jlbpartners_header_section',
			'type'        => 'url',
			'priority'    => 10,
		)
	);

	// Header CTA Text setting
	$wp_customize->add_setting(
		'jlbpartners_header_cta_text',
		array(
			'default'           => __( 'Contact Us', 'jlbpartners' ),
			'sanitize_callback' => 'sanitize_text_field',
			'transport'         => 'refresh',
		)
	);

	// Header CTA Text control
	$wp_customize->add_control(
		'jlbpartners_header_cta_text',
		array(
			'label'       => __( 'Header CTA Text', 'jlbpartners' ),
			'description' => __( 'Enter the text for the header call-to-action button.', 'jlbpartners' ),
			'section'     => 'jlbpartners_header_section',
			'type'        => 'text',
			'priority'    => 20,
		)
	);
}
add_action( 'customize_register', 'jlbpartners_customize_register' );
