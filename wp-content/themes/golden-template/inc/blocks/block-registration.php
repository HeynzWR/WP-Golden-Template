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

    // About Section Block.
    acf_register_block_type(
        array(
            'name'            => 'about-section',
            'title'           => __( 'About Section', 'golden-template' ),
            'description'     => __( 'About us section with statistics', 'golden-template' ),
            'render_template' => get_template_directory() . '/blocks/about/template.php',
            'category'        => 'golden-template-blocks',
            'icon'            => array(
                'src'        => 'groups',
                'foreground' => '#00a400',
            ),
            'keywords'        => array( 'about', 'stats', 'content' ),
            'mode'            => 'preview',
            'supports'        => $default_supports,
            'example'         => $default_example,
        )
    );

	// Milestones Block.
	acf_register_block_type(
		array(
			'name'            => 'milestones',
			'title'           => __( 'Milestones', 'golden-template' ),
			'description'     => __( 'A timeline block displaying milestones with years and events.', 'golden-template' ),
			'render_template' => get_template_directory() . '/blocks/milestones/template.php',
			'category'        => 'golden-template-blocks',
			'icon'            => array(
				'src'        => 'calendar-alt',
				'foreground' => '#00a400',
			),
			'keywords'        => array( 'milestones', 'timeline', 'history', 'events' ),
			'mode'            => 'preview',
			'supports'        => $default_supports,
			'example'         => $default_example,
		)
	);
 
	// Accordion Block.
	acf_register_block_type(
		array(
			'name'            => 'accordion',
			'title'           => __( 'Accordion', 'golden-template' ),
			'description'     => __( 'A collapsible accordion block with multiple items. Each item has a title, optional subtitle, and rich text description.', 'golden-template' ),
			'render_template' => get_template_directory() . '/blocks/accordion/template.php',
			'category'        => 'golden-template-blocks',
			'icon'            => array(
				'src'        => 'list-view',
				'foreground' => '#00a400',
			),
			'keywords'        => array( 'accordion', 'collapse', 'toggle', 'faq', 'expand' ),
			'mode'            => 'preview',
			'supports'        => $default_supports,
			'example'         => $default_example,
		)
	);
 
    // Title Block.
    acf_register_block_type(
        array(
            'name'            => 'title-block',
            'title'           => __( 'Title Block', 'golden-template' ),
            'description'     => __( 'A simple title block with heading, text, and button.', 'golden-template' ),
            'render_template' => get_template_directory() . '/blocks/title-block/template.php',
            'category'        => 'golden-template-blocks',
            'icon'            => array(
                'src'        => 'heading',
                'foreground' => '#00a400',
            ),
            'keywords'        => array( 'title', 'heading', 'text' ),
            'mode'            => 'preview',
            'supports'        => $default_supports,
            'example'         => $default_example,
        )
    );

    // Line Block.
    acf_register_block_type(
        array(
            'name'            => 'line-block',
            'title'           => __( 'Line Block', 'golden-template' ),
            'description'     => __( 'A block with a vertical line and text.', 'golden-template' ),
            'render_template' => get_template_directory() . '/blocks/line-block/template.php',
            'category'        => 'golden-template-blocks',
            'icon'            => array(
                'src'        => 'minus',
                'foreground' => '#00a400',
            ),
            'keywords'        => array( 'line', 'text', 'separator' ),
            'mode'            => 'preview',
            'supports'        => $default_supports,
            'example'         => $default_example,
        )
    );

    // Featured Section Block.
    acf_register_block_type(
        array(
            'name'            => 'featured-section',
            'title'           => __( 'Featured Section', 'golden-template' ),
            'description'     => __( 'A section to display featured projects.', 'golden-template' ),
            'render_template' => get_template_directory() . '/blocks/featured-section/template.php',
            'category'        => 'golden-template-blocks',
            'icon'            => array(
                'src'        => 'grid-view',
                'foreground' => '#00a400',
            ),
            'keywords'        => array( 'featured', 'projects', 'section' ),
            'mode'            => 'preview',
            'supports'        => $default_supports,
            'example'         => $default_example,
        )
    );

    // Service Slider Block.
    acf_register_block_type(
        array(
            'name'            => 'service-slider',
            'title'           => __( 'Service Slider', 'golden-template' ),
            'description'     => __( 'A block to display service slider.', 'golden-template' ),
            'render_template' => get_template_directory() . '/blocks/service-slider/template.php',
            'category'        => 'golden-template-blocks',
            'icon'            => array(
                'src'        => 'grid-view',
                'foreground' => '#00a400',
            ),
            'keywords'        => array( 'service', 'slider', 'section' ),
            'mode'            => 'preview',
            'supports'        => $default_supports,
            'example'         => $default_example,
        )
    );

    // Map Block.
    acf_register_block_type(
        array(
            'name'            => 'map-block',
            'title'           => __( 'Map Block', 'golden-template' ),
            'description'     => __( 'A block to display an interactive map with location cards.', 'golden-template' ),
            'render_template' => get_template_directory() . '/blocks/map-block/template.php',
            'category'        => 'golden-template-blocks',
            'icon'            => array(
                'src'        => 'location-alt',
                'foreground' => '#00a400',
            ),
            'keywords'        => array( 'map', 'location', 'office', 'contact' ),
            'mode'            => 'preview',
            'supports'        => $default_supports,
            'example'         => $default_example,
            'enqueue_assets'  => function() {
                wp_enqueue_style(
                    'golden-template-map-block',
                    get_template_directory_uri() . '/blocks/map-block/map-block.css',
                    array(),
                    GOLDEN_TEMPLATE_VERSION
                );
                wp_enqueue_script(
                    'golden-template-map-block',
                    get_template_directory_uri() . '/blocks/map-block/map-block.js',
                    array( 'jquery' ),
                    GOLDEN_TEMPLATE_VERSION,
                    true
                );
            },
        )
    );

    // Image Block.
    acf_register_block_type(
        array(
            'name'            => 'image-block',
            'title'           => __( 'Image Block', 'golden-template' ),
            'description'     => __( 'A block to upload and display an image with full accessibility support.', 'golden-template' ),
            'render_template' => get_template_directory() . '/blocks/image-block/template.php',
            'category'        => 'golden-template-blocks',
            'icon'            => array(
                'src'        => 'format-image',
                'foreground' => '#00a400',
            ),
            'keywords'        => array( 'image', 'upload', 'photo', 'picture' ),
            'mode'            => 'preview',
            'supports'        => $default_supports,
            'example'         => $default_example,
            'enqueue_assets'  => function() {
                wp_enqueue_style(
                    'golden-template-image-block',
                    get_template_directory_uri() . '/blocks/image-block/image-block.css',
                    array(),
                    GOLDEN_TEMPLATE_VERSION
                );
            },
        )
    );

    // Filter Cards Block.
    acf_register_block_type(
        array(
            'name'            => 'filter-cards',
            'title'           => __( 'Filter Cards', 'golden-template' ),
            'description'     => __( 'A block to display filterable leadership cards.', 'golden-template' ),
            'render_template' => get_template_directory() . '/blocks/filter-cards/template.php',
            'category'        => 'golden-template-blocks',
            'icon'            => array(
                'src'        => 'filter',
                'foreground' => '#00a400',
            ),
            'keywords'        => array( 'filter', 'cards', 'leadership', 'grid' ),
            'mode'            => 'preview',
            'supports'        => $default_supports,
            'example'         => $default_example,
            'enqueue_assets'  => function() {
                wp_enqueue_style(
                    'golden-template-filter-cards',
                    get_template_directory_uri() . '/blocks/filter-cards/filter-cards.css',
                    array(),
                    GOLDEN_TEMPLATE_VERSION
                );
                wp_enqueue_script(
                    'golden-template-filter-cards',
                    get_template_directory_uri() . '/blocks/filter-cards/filter-cards.js',
                    array(),
                    GOLDEN_TEMPLATE_VERSION,
                    true
                );
            },
        )
    );

    // Rich Text Editor Block.
    acf_register_block_type(
        array(
            'name'            => 'rich-text-editor',
            'title'           => __( 'Rich Text Editor', 'golden-template' ),
            'description'     => __( 'A flexible rich text block with full WYSIWYG editor and toggleable decorative lines.', 'golden-template' ),
            'render_template' => get_template_directory() . '/blocks/rich-text-editor/template.php',
            'category'        => 'golden-template-blocks',
            'icon'            => array(
                'src'        => 'edit',
                'foreground' => '#00a400',
            ),
            'keywords'        => array( 'text', 'editor', 'content', 'wysiwyg', 'rich' ),
            'mode'            => 'preview',
            'supports'        => $default_supports,
            'example'         => $default_example,
            'enqueue_assets'  => function() {
                wp_enqueue_style(
                    'golden-template-rich-text-editor',
                    get_template_directory_uri() . '/blocks/rich-text-editor/rich-text-editor.css',
                    array(),
                    GOLDEN_TEMPLATE_VERSION
                );
            },
        )
    );

    // Location Cards Block.
    acf_register_block_type(
        array(
            'name'            => 'location-cards',
            'title'           => __( 'Location Cards', 'golden-template' ),
            'description'     => __( 'Display location cards with title and content.', 'golden-template' ),
            'render_template' => get_template_directory() . '/blocks/location-cards/template.php',
            'category'        => 'golden-template-blocks',
            'icon'            => array(
                'src'        => 'location-alt',
                'foreground' => '#00a400',
            ),
            'keywords'        => array( 'location', 'cards', 'locations'),
            'mode'            => 'preview',
            'supports'        => $default_supports,
            'example'         => $default_example,
            'enqueue_assets'  => function() {
                wp_enqueue_style(
                    'golden-template-location-cards',
                    get_template_directory_uri() . '/blocks/location-cards/location-cards.css',
                    array(),
                    GOLDEN_TEMPLATE_VERSION
                );
                wp_enqueue_script(
                    'golden-template-location-cards',
                    get_template_directory_uri() . '/blocks/location-cards/location-cards.js',
                    array( 'jquery' ),
                    GOLDEN_TEMPLATE_VERSION,
                    true
                );
            },
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
		'about'            => '/blocks/about/about.css',
		'title-block'      => '/blocks/title-block/title-block.css',
		'line-block'       => '/blocks/line-block/line-block.css',
		'featured-section' => '/blocks/featured-section/featured-section.css',
		'milestones'       => '/blocks/milestones/milestones.css',
		'accordion'        => '/blocks/accordion/accordion.css',
		'service-slider'   => '/blocks/service-slider/service-slider.css',
		'rich-text-editor' => '/blocks/rich-text-editor/rich-text-editor.css',
		'location-cards'   => '/blocks/location-cards/location-cards.css',
		'filter-cards'     => '/blocks/filter-cards/filter-cards.css',
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

	// Map block and Image block are handled via enqueue_assets callbacks,
	// but we also enqueue them here for consistency in preview mode.
	if ( file_exists( get_template_directory() . '/blocks/map-block/map-block.css' ) ) {
		wp_enqueue_style(
			'golden-template-map-block-editor',
			get_template_directory_uri() . '/blocks/map-block/map-block.css',
			array( 'golden-template-blocks-editor' ),
			filemtime( get_template_directory() . '/blocks/map-block/map-block.css' )
		);
	}

	if ( file_exists( get_template_directory() . '/blocks/image-block/image-block.css' ) ) {
		wp_enqueue_style(
			'golden-template-image-block-editor',
			get_template_directory_uri() . '/blocks/image-block/image-block.css',
			array( 'golden-template-blocks-editor' ),
			filemtime( get_template_directory() . '/blocks/image-block/image-block.css' )
		);
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

