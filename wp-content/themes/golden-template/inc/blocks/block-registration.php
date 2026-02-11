<?php
/**
 * Block Registration
 *
 * Registers all ACF Blocks for the JLB Partners theme.
 *
 * @package GoldenTemplate
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Get default block supports configuration
 *
 * @return array Default supports configuration
 */
function golden_template_get_default_block_supports() {
	return array(
		'align'           => false,
		'mode'            => true,
		'jsx'             => true,
		'anchor'          => true,
		'customClassName' => true,
		'multiple'        => true,
		'reusable'        => false,
		'lock'            => false,
	);
}

/**
 * Get default block example configuration
 *
 * @return array Default example configuration
 */
function golden_template_get_default_block_example() {
	return array(
		'attributes' => array(
			'mode' => 'preview',
			'data' => array(),
		),
	);
}

/**
 * Register ACF Blocks
 */
function golden_template_register_acf_blocks() {
	// Check if ACF function exists.
	if ( ! function_exists( 'acf_register_block_type' ) ) {
		// ACF Pro is not active - show admin notice
		add_action( 'admin_notices', function() {
			echo '<div class="notice notice-error"><p><strong>Hero Block Error:</strong> ACF Pro is not active. Please activate Advanced Custom Fields Pro to use custom blocks.</p></div>';
		});
		return;
	}

	// Get default configurations.
	$default_supports = golden_template_get_default_block_supports();
	$default_example  = golden_template_get_default_block_example();

	// Hero Section Block.
	acf_register_block_type(
		array(
			'name'            => 'hero-section',
			'title'           => __( 'Hero Section', 'golden-template' ),
			'description'     => __( 'Full-width hero with background image, headline, text & repeater items', 'golden-template' ),
			'render_template' => get_template_directory() . '/blocks/hero-section/template.php',
			'category'        => 'golden-template-blocks',
			'icon'            => array(
				'src'        => 'cover-image',
				'foreground' => '#00a400',
			),
			'keywords'        => array( 'hero', 'banner', 'header', 'cover', 'landing' ),
			'mode'            => 'preview',
			'supports'        => $default_supports,
			'example'         => $default_example,
		)
	);

}
add_action( 'acf/init', 'golden_template_register_acf_blocks', 20 ); // Later priority to ensure ACF is fully loaded

/**
 * Register fallback blocks that work without ACF Pro license
 */
function golden_template_register_fallback_blocks() {
	// Always register a simple test block that works without ACF
	register_block_type( 'golden-template/simple-hero', array(
		'title'           => __( 'Simple Hero (No ACF)', 'golden-template' ),
		'description'     => __( 'Simple hero block that always works', 'golden-template' ),
		'category'        => 'golden-template-blocks',
		'icon'            => 'cover-image',
		'keywords'        => array( 'hero', 'banner', 'simple' ),
		'supports'        => array(
			'anchor' => true,
			'customClassName' => true,
		),
		'attributes'      => array(
			'title' => array(
				'type'    => 'string',
				'default' => 'Simple Hero Title',
			),
		),
		'render_callback' => 'golden_template_render_simple_hero',
	) );
	
	// Only register ACF fallback if ACF Pro is not working properly
	if ( function_exists( 'acf_register_block_type' ) ) {
		// Check if ACF Pro has license issues
		$has_license = true;
		if ( function_exists( 'acf_get_setting' ) ) {
			$license = acf_get_setting( 'license' );
			$has_license = ! empty( $license );
		}
		
		// If ACF Pro has a valid license, don't register fallback
		if ( $has_license ) {
			return;
		}
	}
	
	// Register native WordPress block as fallback
	register_block_type( 'golden-template/hero-fallback', array(
		'title'           => __( 'Hero Section (Fallback)', 'golden-template' ),
		'description'     => __( 'Hero section that works without ACF Pro license', 'golden-template' ),
		'category'        => 'golden-template-blocks',
		'icon'            => 'cover-image',
		'keywords'        => array( 'hero', 'banner', 'header' ),
		'supports'        => array(
			'anchor' => true,
			'customClassName' => true,
		),
		'attributes'      => array(
			'title' => array(
				'type'    => 'string',
				'default' => 'Hero Title',
			),
			'content' => array(
				'type'    => 'string',
				'default' => 'Hero content goes here...',
			),
			'imageUrl' => array(
				'type'    => 'string',
				'default' => '',
			),
		),
		'render_callback' => 'golden_template_render_fallback_hero',
	) );
}
add_action( 'init', 'golden_template_register_fallback_blocks' );

/**
 * Render simple hero block (always works)
 *
 * @param array  $attributes Block attributes.
 * @param string $_content   Block content (unused but required by block render callback signature).
 * @return string Rendered block HTML.
 */
function golden_template_render_simple_hero( $attributes, $_content ) { // phpcs:ignore VariableAnalysis.CodeAnalysis.VariableAnalysis.UnusedVariable -- Parameter required by block render callback signature.
	$title = $attributes['title'] ?? 'Simple Hero Title';
	
	ob_start();
	?>
	<section class="simple-hero" style="padding: 80px 20px; background: linear-gradient(135deg, #00a400 0%, #004500 100%); color: white; text-align: center; min-height: 400px; display: flex; align-items: center; justify-content: center;">
		<div style="max-width: 800px; margin: 0 auto;">
			<h1 style="font-size: 3rem; margin-bottom: 1rem; text-shadow: 2px 2px 4px rgba(0,0,0,0.5);">
				<?php echo esc_html( $title ); ?>
			</h1>
			<p style="font-size: 1.2rem; margin-bottom: 2rem;">This is a simple hero block that works without any dependencies!</p>
			<div style="background: rgba(255,255,255,0.1); padding: 1rem; border-radius: 8px; margin-top: 2rem;">
				<p style="margin: 0; font-size: 0.9rem;">✅ <strong>Simple Hero Block Working!</strong> - No ACF Pro required</p>
			</div>
		</div>
	</section>
	<?php
	return ob_get_clean();
}

/**
 * Render fallback hero block
 *
 * @param array  $attributes Block attributes.
 * @param string $_content   Block content (unused but required by block render callback signature).
 * @return string Rendered block HTML.
 */
 function golden_template_render_fallback_hero( $attributes, $_content ) { // phpcs:ignore VariableAnalysis.CodeAnalysis.VariableAnalysis.UnusedVariable -- Parameter required by block render callback signature.
	$title = $attributes['title'] ?? 'Hero Title';
	$image_url = $attributes['imageUrl'] ?? '';
	
	 //Use the same template structure as ACF version
	ob_start();
	?>
	<section class="hero-section fallback-hero" style="<?php echo $image_url ? 'background-image: url(' . esc_url( $image_url ) . '); background-size: cover; background-position: center;' : 'background: linear-gradient(135deg, #00a400 0%, #004500 100%);'; ?> padding: 80px 20px; text-align: center; min-height: 500px; display: flex; align-items: center; justify-content: center; color: white;">
		<div class="container" style="max-width: 1200px; margin: 0 auto;">
			<h1 class="hero__title" style="font-size: 3rem; margin-bottom: 1.5rem; font-weight: bold; text-shadow: 2px 2px 4px rgba(0,0,0,0.5); line-height: 1.2;">
				<?php echo esc_html( $title ); ?>
			</h1>
			
 			<div style="margin-top: 2rem; padding: 1.5rem; background: rgba(255,255,255,0.95); border-radius: 12px; color: #333; box-shadow: 0 4px 20px rgba(0,0,0,0.1);">
 				<h3 style="margin: 0 0 1rem 0; color: #00a400;">🎯 Hero Section Component</h3>
 				<p style="margin: 0 0 1rem 0;"><strong>Status:</strong> Working with fallback mode</p>
 				<p style="margin: 0; font-size: 0.9rem; opacity: 0.8;">To use the full-featured version with ACF fields, please activate ACF Pro with a valid license.</p>
 			</div>
 		</div>
 	</section>
 	<?php
 	return ob_get_clean();
 }

/**
 * Register custom block category
 */
function golden_template_block_categories( $categories ) {
	return array_merge(
		array(
			array(
				'slug'  => 'golden-template-blocks',
				'title' => __( 'JLB Partners Components', 'golden-template' ),
				'icon'  => 'layout',
			),
		),
		$categories
	);
}
add_filter( 'block_categories_all', 'golden_template_block_categories', 5, 1 ); // Higher priority

/**
 * Enqueue block editor assets
 */
function golden_template_block_editor_assets() {
	// Enqueue fonts for editor (same as frontend).
	wp_enqueue_style(
		'golden-template-google-fonts-editor',
		'https://fonts.googleapis.com/css2?family=Archivo:ital,wght@0,100..900;1,100..900&family=Geist:wght@100..900&family=Libre+Baskerville:ital,wght@0,400;0,700;1,400&display=swap',
		array(),
		null
	);
	
	// Enqueue block styles for editor (ACF field styling).
	wp_enqueue_style(
		'golden-template-blocks-editor',
		get_template_directory_uri() . '/assets/css/admin/blocks-editor.css',
		array( 'golden-template-google-fonts-editor' ),
		golden_template_get_asset_version( 'assets/css/admin/blocks-editor.css' )
	);

	// Enqueue all block-specific CSS files for accurate preview rendering.
	// These match what's loaded on the frontend to ensure preview looks identical.
	$block_styles = array(
		'hero-section'     => '/blocks/hero-section/hero-section.css',
	);

	foreach ( $block_styles as $handle => $path ) {
		$full_path = get_template_directory() . $path;
		if ( file_exists( $full_path ) ) {
			wp_enqueue_style(
				'golden-template-' . $handle . '-editor',
				get_template_directory_uri() . $path,
				array( 'golden-template-blocks-editor' ),
				filemtime( $full_path )
			);
		}
	}

	// Enqueue Slick Carousel CSS for blocks that use it (featured-section, service-slider).
	// This ensures carousel blocks display correctly in the editor preview.
	wp_enqueue_style(
		'slick-carousel-editor',
		'https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.css',
		array(),
		'1.8.1'
	);
	wp_enqueue_style(
		'slick-carousel-theme-editor',
		'https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick-theme.css',
		array( 'slick-carousel-editor' ),
		'1.8.1'
	);

	// Enqueue block toolbar customization script.
	wp_enqueue_script(
		'golden-template-block-toolbar-customization',
		get_template_directory_uri() . '/assets/js/admin/block-toolbar-customization.js',
		array( 'wp-blocks', 'wp-element', 'wp-components', 'wp-block-editor', 'wp-compose', 'wp-hooks', 'wp-data', 'wp-dom-ready' ),
		golden_template_get_asset_version( 'assets/js/admin/block-toolbar-customization.js' ),
		true
	);

	// Enqueue ACF media metadata fetch buttons.
	wp_enqueue_style(
		'golden-template-acf-media-fetch',
		get_template_directory_uri() . '/assets/css/admin/acf-media-fetch.css',
		array(),
		golden_template_get_asset_version( 'assets/css/admin/acf-media-fetch.css' )
	);

	wp_enqueue_script(
		'golden-template-acf-media-fetch',
		get_template_directory_uri() . '/assets/js/admin/acf-media-fetch.js',
		array( 'jquery', 'acf-input' ),
		golden_template_get_asset_version( 'assets/js/admin/acf-media-fetch.js' ),
		true
	);
}
add_action( 'enqueue_block_editor_assets', 'golden_template_block_editor_assets' );

/**
 * Force register blocks on init (backup method)
 */
function golden_template_force_register_blocks() {
	// Always register the simple hero block
	register_block_type( 'golden-template/simple-hero-force', array(
		'title'           => __( 'Simple Hero FORCE', 'golden-template' ),
		'description'     => __( 'Force registered hero block', 'golden-template' ),
		'category'        => 'golden-template-blocks',
		'icon'            => 'cover-image',
		'keywords'        => array( 'hero', 'banner', 'force' ),
		'render_callback' => function() {
			return '<div style="padding: 60px 20px; background: linear-gradient(135deg, #007cba 0%, #005177 100%); color: white; text-align: center; min-height: 300px; display: flex; align-items: center; justify-content: center;"><div><h1 style="margin: 0 0 1rem 0; font-size: 2.5rem;">🚀 Force Hero Block</h1><p style="margin: 0; font-size: 1.2rem;">This block was force-registered and should always work!</p></div></div>';
		}
	) );
	
}
add_action( 'init', 'golden_template_force_register_blocks', 25 );

