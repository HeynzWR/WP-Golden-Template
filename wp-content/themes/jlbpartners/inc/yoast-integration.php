<?php
/**
 * Yoast SEO Integration
 *
 * @package JLBPartners
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Check if Yoast SEO is active
 *
 * @return bool
 */
function jlbpartners_has_yoast() {
	return defined( 'WPSEO_VERSION' );
}

/**
 * Get Yoast SEO title or fallback
 *
 * @return string
 */
function jlbpartners_get_seo_title() {
	if ( jlbpartners_has_yoast() && function_exists( 'YoastSEO' ) ) {
		return YoastSEO()->meta->for_current_page()->title;
	}

	return wp_get_document_title();
}

/**
 * Get Yoast SEO description or fallback
 *
 * @return string
 */
function jlbpartners_get_seo_description() {
	if ( jlbpartners_has_yoast() && function_exists( 'YoastSEO' ) ) {
		return YoastSEO()->meta->for_current_page()->description;
	}

	return get_bloginfo( 'description' );
}

/**
 * Add schema.org markup support
 */
function jlbpartners_schema_markup() {
	if ( is_front_page() ) {
		$schema_type = 'WebSite';
	} elseif ( is_single() ) {
		$schema_type = 'Article';
	} elseif ( is_page() ) {
		$schema_type = 'WebPage';
	} else {
		$schema_type = 'WebPage';
	}

	echo ' itemscope itemtype="https://schema.org/' . esc_attr( $schema_type ) . '"';
}

/**
 * Ensure Yoast outputs Open Graph tags
 */
function jlbpartners_ensure_yoast_og() {
	if ( ! jlbpartners_has_yoast() ) {
		add_action( 'wp_head', 'jlbpartners_add_basic_og_tags', 5 );
	}
}
add_action( 'wp', 'jlbpartners_ensure_yoast_og' );

/**
 * Add basic Open Graph tags if Yoast is not active
 */
function jlbpartners_add_basic_og_tags() {
	global $post;
	?>
	<!-- Open Graph Meta Tags -->
	<meta property="og:locale" content="<?php echo esc_attr( get_locale() ); ?>">
	<meta property="og:type" content="<?php echo esc_attr( is_single() ? 'article' : 'website' ); ?>">
	<meta property="og:title" content="<?php echo esc_attr( wp_get_document_title() ); ?>">
	<meta property="og:description" content="<?php echo esc_attr( get_bloginfo( 'description' ) ); ?>">
	<meta property="og:url" content="<?php echo esc_url( get_permalink() ); ?>">
	<meta property="og:site_name" content="<?php echo esc_attr( get_bloginfo( 'name' ) ); ?>">
	
	<?php if ( has_post_thumbnail() && is_singular() ) : ?>
		<meta property="og:image" content="<?php echo esc_url( get_the_post_thumbnail_url( $post, 'large' ) ); ?>">
	<?php endif; ?>
	
	<!-- Twitter Card Meta Tags -->
	<meta name="twitter:card" content="summary_large_image">
	<meta name="twitter:title" content="<?php echo esc_attr( wp_get_document_title() ); ?>">
	<meta name="twitter:description" content="<?php echo esc_attr( get_bloginfo( 'description' ) ); ?>">
	
	<?php if ( has_post_thumbnail() && is_singular() ) : ?>
		<meta name="twitter:image" content="<?php echo esc_url( get_the_post_thumbnail_url( $post, 'large' ) ); ?>">
	<?php endif; ?>
	<?php
}

/**
 * Add canonical URL if not handled by Yoast
 */
function jlbpartners_add_canonical() {
	if ( ! jlbpartners_has_yoast() && is_singular() ) {
		echo '<link rel="canonical" href="' . esc_url( get_permalink() ) . '">' . "\n";
	}
}
add_action( 'wp_head', 'jlbpartners_add_canonical', 10 );

/**
 * Add breadcrumb support for Yoast
 */
function jlbpartners_yoast_breadcrumbs() {
	if ( jlbpartners_has_yoast() && function_exists( 'yoast_breadcrumb' ) ) {
		yoast_breadcrumb( '<div class="breadcrumbs" aria-label="Breadcrumb">', '</div>' );
	}
}

/**
 * Get current page/post schema data
 *
 * @return array
 */
function jlbpartners_get_schema_data() {
	$schema = array(
		'@context' => 'https://schema.org',
		'@type'    => 'Organization',
		'name'     => get_bloginfo( 'name' ),
		'url'      => home_url( '/' ),
	);

	$logo_id = get_option( 'jlbpartners_logo' );
	if ( $logo_id ) {
		$logo_url = wp_get_attachment_url( $logo_id );
		if ( $logo_url ) {
			$schema['logo'] = $logo_url;
		}
	}

	return $schema;
}
