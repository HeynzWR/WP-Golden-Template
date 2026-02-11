<?php
/**
 * Advanced Cache Optimization & Asset Versioning
 *
 * @package JLBPartners
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Define asset versioning method
 */
if ( ! defined( 'JLBPARTNERS_ASSET_VERSIONING' ) ) {
	define( 'JLBPARTNERS_ASSET_VERSIONING', 'md5' );
}

/**
 * Get asset version using MD5 hash method
 *
 * @param string $file_path Relative path to asset file.
 * @return string Version string for cache busting.
 */
function jlbpartners_get_asset_version_md5( $file_path ) {
	$full_path = JLBPARTNERS_THEME_DIR . '/' . ltrim( $file_path, '/' );
	
	$file_mtime = filemtime( $full_path );
	$cache_key = 'jlbpartners_asset_version_md5_' . md5( $file_path . $file_mtime );
	
	$cached_version = wp_cache_get( $cache_key );
	if ( false !== $cached_version ) {
		return $cached_version;
	}
	
	global $wp_filesystem;
	if ( empty( $wp_filesystem ) ) {
		require_once ABSPATH . '/wp-admin/includes/file.php';
		WP_Filesystem();
	}
	
	if ( ! $wp_filesystem || ! $wp_filesystem->exists( $full_path ) ) {
		return JLBPARTNERS_VERSION;
	}
	
	$file_content = $wp_filesystem->get_contents( $full_path );
	$file_hash = md5( $file_content );
	
	$short_hash = substr( $file_hash, 0, 12 );
	$version = JLBPARTNERS_VERSION . '-' . $short_hash;
	
	wp_cache_set( $cache_key, $version, '', DAY_IN_SECONDS );
	
	return $version;
}

/**
 * Get asset version using file modification time method
 *
 * @param string $file_path Relative path to asset file.
 * @return string Version string for cache busting.
 */
function jlbpartners_get_asset_version_filemtime( $file_path ) {
	$full_path = JLBPARTNERS_THEME_DIR . '/' . ltrim( $file_path, '/' );
	
	if ( ! file_exists( $full_path ) ) {
		return JLBPARTNERS_VERSION;
	}
	
	$file_mtime = filemtime( $full_path );
	return JLBPARTNERS_VERSION . '.' . $file_mtime;
}

/**
 * Get asset version using theme version only method
 *
 * @param string $_file_path Relative path to asset file (unused but kept for consistency with other versioning methods).
 * @return string Version string for cache busting.
 */
function jlbpartners_get_asset_version_theme( $_file_path ) { // phpcs:ignore VariableAnalysis.CodeAnalysis.VariableAnalysis.UnusedVariable -- Parameter kept for consistency with other versioning method signatures.
	return JLBPARTNERS_VERSION;
}

/**
 * Togglable asset versioning system
 *
 * @param string $file_path Relative path to asset file.
 * @return string Version string for cache busting.
 */
function jlbpartners_get_asset_version( $file_path = '' ) {
	if ( empty( $file_path ) ) {
		return JLBPARTNERS_VERSION;
	}
	
	$full_path = JLBPARTNERS_THEME_DIR . '/' . ltrim( $file_path, '/' );
	
	if ( ! file_exists( $full_path ) ) {
		return JLBPARTNERS_VERSION;
	}
	
	$method = defined( 'JLBPARTNERS_ASSET_VERSIONING' ) ? JLBPARTNERS_ASSET_VERSIONING : 'md5';
	$method = apply_filters( 'jlbpartners_asset_versioning_method', $method );
	
	switch ( $method ) {
		case 'md5':
			return jlbpartners_get_asset_version_md5( $file_path );
			
		case 'filemtime':
			return jlbpartners_get_asset_version_filemtime( $file_path );
			
		case 'theme':
			return jlbpartners_get_asset_version_theme( $file_path );
			
		default:
			$custom_version = apply_filters( 'jlbpartners_get_asset_version_custom', null, $file_path, $method );
			if ( null !== $custom_version ) {
				return $custom_version;
			}
			
			return jlbpartners_get_asset_version_md5( $file_path );
	}
}

/**
 * Cache-friendly block detection
 *
 * @param int $post_id Post ID to check.
 * @return array Array of used block names.
 */
function jlbpartners_get_cached_used_blocks( $post_id = null ) {
	if ( ! $post_id ) {
		$post_id = get_the_ID();
	}
	
	if ( ! $post_id ) {
		return array();
	}
	
	$cache_key = 'jlbpartners_blocks_' . $post_id;
	$cached_blocks = get_transient( $cache_key );
	
	if ( false !== $cached_blocks ) {
		return $cached_blocks;
	}
	
	$post = get_post( $post_id );
	if ( ! $post || ! has_blocks( $post->post_content ) ) {
		return array();
	}
	
	$parsed_blocks = parse_blocks( $post->post_content );
	$blocks = jlbpartners_extract_block_names( $parsed_blocks );
	$blocks = array_unique( $blocks );
	
	set_transient( $cache_key, $blocks, HOUR_IN_SECONDS );
	
	return $blocks;
}

/**
 * Clear block cache when post is updated
 *
 * @param int $post_id Post ID being updated.
 */
function jlbpartners_clear_block_cache( $post_id ) {
	$cache_key = 'jlbpartners_blocks_' . $post_id;
	delete_transient( $cache_key );
}
add_action( 'save_post', 'jlbpartners_clear_block_cache' );

/**
 * Add cache-friendly headers for static assets
 */
function jlbpartners_add_cache_headers() {
	if ( ! isset( $_SERVER['REQUEST_URI'] ) || empty( $_SERVER['REQUEST_URI'] ) ) {
		return;
	}
	
	$request_uri = sanitize_text_field( wp_unslash( $_SERVER['REQUEST_URI'] ) );
	
	if ( strpos( $request_uri, '/wp-content/themes/jlbpartners/' ) !== false ) {
		if ( preg_match( '/\.(css|js|png|jpg|jpeg|gif|webp|svg|woff|woff2|ttf|eot)$/i', $request_uri ) ) {
			header( 'Cache-Control: public, max-age=31536000, immutable' );
			header( 'Expires: ' . gmdate( 'D, d M Y H:i:s', time() + 31536000 ) . ' GMT' );
		}
	}
}
add_action( 'init', 'jlbpartners_add_cache_headers' );

/**
 * Optimize database queries for caching plugins
 */
function jlbpartners_optimize_queries() {
	$cache_key = 'jlbpartners_theme_options';
	$cached_options = wp_cache_get( $cache_key );
	
	if ( false === $cached_options ) {
		$cached_options = array(
			'logo' => get_option( 'jlbpartners_logo' ),
			'placeholder_image' => get_option( 'jlbpartners_placeholder_image' ),
			'footer_heading' => get_option( 'jlbpartners_footer_heading' ),
			'footer_email' => get_option( 'jlbpartners_footer_email' ),
			'footer_address' => get_option( 'jlbpartners_footer_address' ),
		);
		
		wp_cache_set( $cache_key, $cached_options, '', HOUR_IN_SECONDS );
	}
	
	return $cached_options;
}

/**
 * Preload critical resources for better performance
 */
function jlbpartners_preload_critical_assets() {
	echo '<link rel="preload" href="' . esc_url( JLBPARTNERS_THEME_URI . '/assets/css/frontend/main.css?ver=' . jlbpartners_get_asset_version( 'assets/css/frontend/main.css' ) ) . '" as="style" onload="this.onload=null;this.rel=\'stylesheet\'">' . "\n";
}
add_action( 'wp_head', 'jlbpartners_preload_critical_assets', 1 );
