<?php
/**
 * Customizer settings for header CTA
 *
 * @package GoldenTemplate
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Register customizer settings
 */
function golden_template_customize_register( $wp_customize ) {
	// Add Header section
	$wp_customize->add_section(
		'golden_template_header_section',
		array(
			'title'    => __( 'Header Settings', 'golden-template' ),
			'priority' => 30,
		)
	);

	// Header CTA URL setting
	$wp_customize->add_setting(
		'golden_template_header_cta_url',
		array(
			'default'           => '#',
			'sanitize_callback' => 'esc_url_raw',
			'transport'         => 'refresh',
		)
	);

	// Header CTA URL control
	$wp_customize->add_control(
		'golden_template_header_cta_url',
		array(
			'label'       => __( 'Header CTA URL', 'golden-template' ),
			'description' => __( 'Enter the URL for the header call-to-action button.', 'golden-template' ),
			'section'     => 'golden_template_header_section',
			'type'        => 'url',
			'priority'    => 10,
		)
	);

	// Header CTA Text setting
	$wp_customize->add_setting(
		'golden_template_header_cta_text',
		array(
			'default'           => __( 'Contact Us', 'golden-template' ),
			'sanitize_callback' => 'sanitize_text_field',
			'transport'         => 'refresh',
		)
	);

	// Header CTA Text control
	$wp_customize->add_control(
		'golden_template_header_cta_text',
		array(
			'label'       => __( 'Header CTA Text', 'golden-template' ),
			'description' => __( 'Enter the text for the header call-to-action button.', 'golden-template' ),
			'section'     => 'golden_template_header_section',
			'type'        => 'text',
			'priority'    => 20,
		)
	);
}
add_action( 'customize_register', 'golden_template_customize_register' );
