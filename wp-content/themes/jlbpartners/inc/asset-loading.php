<?php
/**
 * Asset Loading System
 *
 * Handles smart loading of block-specific assets and external library management.
 *
 * @package JLBPartners
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Check which blocks/components are used on the current page
 *
 * @return array Array of block names used on the page.
 */
function jlbpartners_get_used_blocks() {
	global $post;

	if ( ! $post ) {
		return array();
	}

	$blocks = array();

	if ( has_blocks( $post->post_content ) ) {
		$parsed_blocks = parse_blocks( $post->post_content );
		$blocks        = jlbpartners_extract_block_names( $parsed_blocks );
	}

	return array_unique( $blocks );
}

/**
 * Recursively extract block names from parsed blocks
 *
 * @param array $blocks Parsed blocks array.
 * @return array Array of block names.
 */
function jlbpartners_extract_block_names( $blocks ) {
	$block_names = array();

	foreach ( $blocks as $block ) {
		if ( ! empty( $block['blockName'] ) ) {
			$block_names[] = $block['blockName'];
		}

		if ( ! empty( $block['innerBlocks'] ) ) {
			$inner_block_names = jlbpartners_extract_block_names( $block['innerBlocks'] );
			$block_names       = array_merge( $block_names, $inner_block_names );
		}
	}

	return $block_names;
}

/**
 * Check if GSAP should be loaded on current page
 *
 * @return bool True if GSAP should be loaded, false otherwise.
 */
function jlbpartners_should_load_gsap() {
	return true;
}

/**
 * Enqueue GSAP JavaScript
 */
function jlbpartners_enqueue_gsap_js() {
	// Check if GSAP should be loaded on this page
	if ( ! jlbpartners_should_load_gsap() ) {
		return;
	}

	if ( ! wp_script_is( 'gsap', 'enqueued' ) ) {
		// Get file modification time for cache busting
		$theme_dir = defined( 'JLBPARTNERS_THEME_DIR' ) ? JLBPARTNERS_THEME_DIR : get_template_directory();
		$gsap_file = $theme_dir . '/assets/js/frontend/vendor/gsap.min.js';
		$scroll_trigger_file = $theme_dir . '/assets/js/frontend/vendor/scroll-trigger.min.js';
		$gsap_version = file_exists( $gsap_file ) ? filemtime( $gsap_file ) : '1.0.0';
		$scroll_trigger_version = file_exists( $scroll_trigger_file ) ? filemtime( $scroll_trigger_file ) : '1.0.0';
		
		// Enqueue GSAP
		wp_enqueue_script(
			'gsap',
			'https://cdnjs.cloudflare.com/ajax/libs/gsap/3.13.0/gsap.min.js',
			array(),
			$gsap_version,
			true
		);
		
		// Enqueue ScrollTrigger (depends on GSAP)
		// Note: ScrollTrigger version must match GSAP version
		if ( ! wp_script_is( 'scrolltrigger', 'enqueued' ) ) {
			wp_enqueue_script(
				'scrolltrigger',
				'https://cdnjs.cloudflare.com/ajax/libs/gsap/3.13.0/ScrollTrigger.min.js',
				array( 'gsap' ),
				$scroll_trigger_version,
				true
			);
		}
	}
}

/**
 * Enqueue AOS JavaScript
 */
function jlbpartners_enqueue_aos_js() {
	if ( ! wp_script_is( 'aos', 'enqueued' ) ) {
		// Use theme URI constant if available, otherwise fallback to WordPress function
		$theme_uri = defined( 'JLBPARTNERS_THEME_URI' ) ? JLBPARTNERS_THEME_URI : get_template_directory_uri();
		$aos_path = $theme_uri . '/assets/js/frontend/vendor/aos.js';
		
		// Get file modification time for cache busting
		$theme_dir = defined( 'JLBPARTNERS_THEME_DIR' ) ? JLBPARTNERS_THEME_DIR : get_template_directory();
		$aos_file = $theme_dir . '/assets/js/frontend/vendor/aos.js';
		$aos_version = file_exists( $aos_file ) ? filemtime( $aos_file ) : '1.0.0';
		
		// Enqueue AOS
		wp_enqueue_script(
			'aos',
			$aos_path,
			array(),
			$aos_version,
			true
		);
	}
}

/**
 * Enqueue Slick Slider CSS
 */
function jlbpartners_enqueue_slick_css() {
	if ( ! wp_style_is( 'slick-slider', 'enqueued' ) ) {
		wp_enqueue_style(
			'slick-slider',
			'https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.css',
			array(),
			'1.8.1'
		);
	}
}

/**
 * Enqueue Slick Slider JavaScript
 */
function jlbpartners_enqueue_slick_js() {
	if ( ! wp_script_is( 'slick-slider', 'enqueued' ) ) {
		wp_enqueue_script(
			'slick-slider',
			'https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.min.js',
			array( 'jquery' ),
			'1.8.1',
			true
		);
	}
}

/**
 * Enqueue both Slick Slider CSS and JS
 */
function jlbpartners_enqueue_slick_assets() {
	jlbpartners_enqueue_slick_css();
	jlbpartners_enqueue_slick_js();
}

/**
 * Enqueue Slick Lightbox CSS
 */
function jlbpartners_enqueue_slick_lightbox_css() {
	if ( ! wp_style_is( 'slick-lightbox', 'enqueued' ) ) {
		wp_enqueue_style(
			'slick-lightbox',
			'https://cdnjs.cloudflare.com/ajax/libs/slick-lightbox/0.2.12/slick-lightbox.css',
			array( 'slick-slider' ),
			'0.2.12'
		);
	}
}

/**
 * Enqueue Slick Lightbox JavaScript
 */
function jlbpartners_enqueue_slick_lightbox_js() {
	if ( ! wp_script_is( 'slick-lightbox', 'enqueued' ) ) {
		wp_enqueue_script(
			'slick-lightbox',
			'https://cdnjs.cloudflare.com/ajax/libs/slick-lightbox/0.2.12/slick-lightbox.min.js',
			array( 'jquery', 'slick-slider' ),
			'0.2.12',
			true
		);
	}
}

/**
 * Enqueue both Slick Lightbox CSS and JS
 */
function jlbpartners_enqueue_slick_lightbox_assets() {
	jlbpartners_enqueue_slick_lightbox_css();
	jlbpartners_enqueue_slick_lightbox_js();
}

/**
 * Add inline critical CSS for above-the-fold content
 */
function jlbpartners_inline_critical_css() {
	if ( is_admin() || is_customize_preview() ) {
		return;
	}

	$critical_css = '
		.skip-link {
			position: absolute;
			left: -9999px;
			z-index: 999999;
			padding: 1em;
			background: #000;
			color: #fff;
			text-decoration: none;
		}
		.skip-link:focus {
			left: 0;
			top: 0;
		}
		.visually-hidden {
			position: absolute;
			width: 1px;
			height: 1px;
			padding: 0;
			margin: -1px;
			overflow: hidden;
			clip: rect(0, 0, 0, 0);
			white-space: nowrap;
			border-width: 0;
		}
	';

	if ( ! empty( $critical_css ) ) {
		// Escaping CSS content for security
		echo '<style id="jlbpartners-critical-css">' . esc_html( $critical_css ) . '</style>' . "\n";
	}
}
add_action( 'wp_head', 'jlbpartners_inline_critical_css', 1 );

/**
 * Hook GSAP enqueue function
 * Priority 5 ensures it loads before main.js (default priority 10)
 */
add_action( 'wp_enqueue_scripts', 'jlbpartners_enqueue_gsap_js', 5 );

/**
 * Hook AOS enqueue function
 * Priority 5 ensures it loads before main.js (default priority 10)
 */
add_action( 'wp_enqueue_scripts', 'jlbpartners_enqueue_aos_js', 5 );
