<?php
/**
 * JLB Partners Theme Functions
 *
 * @package GoldenTemplate
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Define theme constants.
define( 'GOLDEN_TEMPLATE_VERSION', '1.0.0' );
define( 'GOLDEN_TEMPLATE_THEME_DIR', get_template_directory() );
define( 'GOLDEN_TEMPLATE_THEME_URI', get_template_directory_uri() );

// Template debugging - set to true to enable, false to disable
define( 'GOLDEN_TEMPLATE_DEBUG_TEMPLATES', false );

/**
 * Template debugging system
 */
if ( defined( 'GOLDEN_TEMPLATE_DEBUG_TEMPLATES' ) && GOLDEN_TEMPLATE_DEBUG_TEMPLATES ) {
	
	/**
	 * Track which template files are being used
	 */
	add_filter( 'template_include', 'golden_template_debug_template_include' );
	function golden_template_debug_template_include( $template ) {
		// Store template info for frontend display
		global $golden_template_debug_info;
		$golden_template_debug_info = array(
			'template_file' => basename( $template ),
			'template_path' => $template,
			'page_type'     => golden_template_get_page_type_debug(),
			'post_id'       => get_queried_object_id(),
			'post_title'    => is_singular() ? get_the_title() : '',
		);
		
		return $template;
	}
	
	/**
	 * Get current page type for debugging
	 */
	function golden_template_get_page_type_debug() {
		if ( is_front_page() ) return 'Front Page';
		if ( is_home() ) return 'Blog Home';
		if ( is_page() ) return 'Page';
		if ( is_single() ) return 'Single Post';
		if ( is_category() ) return 'Category Archive';
		if ( is_tag() ) return 'Tag Archive';
		if ( is_archive() ) return 'Archive';
		if ( is_search() ) return 'Search Results';
		if ( is_404() ) return '404 Error';
		return 'Unknown';
	}
	
	/**
	 * Display debug info on frontend
	 */
	add_action( 'wp_footer', 'golden_template_display_template_debug' );
	function golden_template_display_template_debug() {
		global $golden_template_debug_info, $golden_template_debug_blocks;
		
		if ( ! $golden_template_debug_info ) return;
		
		$debug_info = $golden_template_debug_info;
		?>
		<div id="golden-template-template-debug" style="
			position: fixed;
			bottom: 0;
			left: 0;
			background: #080808bf;
			color: white;
			padding: 15px;
			font-family: 'Courier New', monospace;
			font-size: 12px;
			line-height: 1.4;
			z-index: 99999;
			max-width: 450px;
			max-height: 80vh;
			overflow-y: auto;
			border-radius: 0 8px 0 0;
			box-shadow: 0 -2px 10px rgba(0,0,0,0.3);
		">
			<div style="font-weight: bold; margin-bottom: 8px; color: #fff;">
				🔍 Template Debug Info
				<button onclick="this.parentElement.parentElement.style.display='none'" 
					style="float: right; background: rgba(255,255,255,0.2); border: none; color: white; padding: 2px 6px; border-radius: 3px; cursor: pointer;">×</button>
			</div>
			
			<!-- Template Info -->
			<div><strong>Template:</strong> <?php echo esc_html( $debug_info['template_file'] ); ?></div>
			<div><strong>Page Type:</strong> <?php echo esc_html( $debug_info['page_type'] ); ?></div>
			<?php if ( $debug_info['post_id'] ) : ?>
				<div><strong>Post ID:</strong> <?php echo esc_html( $debug_info['post_id'] ); ?></div>
			<?php endif; ?>
			<?php if ( $debug_info['post_title'] ) : ?>
				<div><strong>Title:</strong> <?php echo esc_html( $debug_info['post_title'] ); ?></div>
			<?php endif; ?>
			
			<!-- ACF Blocks Info -->
			<?php if ( ! empty( $golden_template_debug_blocks ) ) : ?>
				<div style="margin-top: 12px; padding-top: 8px; border-top: 1px solid rgba(255,255,255,0.3);">
					<strong>🧩 ACF Blocks (<?php echo count( $golden_template_debug_blocks ); ?>):</strong>
					<?php foreach ( $golden_template_debug_blocks as $block ) : ?>
						<div style="margin: 4px 0; padding-left: 8px; font-size: 11px;">
							<div><strong><?php echo esc_html( $block['title'] ); ?></strong></div>
							<div style="opacity: 0.8;"><?php echo esc_html( $block['name'] ); ?></div>
							<div style="opacity: 0.7; font-size: 10px;"><?php echo esc_html( $block['template_file'] ); ?></div>
						</div>
					<?php endforeach; ?>
				</div>
			<?php endif; ?>
			
			<div style="margin-top: 8px; font-size: 10px; opacity: 0.8; border-top: 1px solid rgba(255,255,255,0.3); padding-top: 8px;">
				Path: <?php echo esc_html( str_replace( ABSPATH, '', $debug_info['template_path'] ) ); ?>
			</div>
		</div>
		<?php
	}
	
	/**
	 * Track ACF blocks being rendered
	 */
	add_filter( 'render_block', 'golden_template_debug_acf_blocks', 10, 2 );
	function golden_template_debug_acf_blocks( $block_content, $block ) {
		// Only track ACF blocks for display
		if ( ! isset( $block['blockName'] ) || strpos( $block['blockName'], 'acf/' ) !== 0 ) {
			return $block_content;
		}
		
		$block_name = $block['blockName'];
		
		// Get registered block type info
		$block_types = WP_Block_Type_Registry::get_instance()->get_all_registered();
		$block_type = $block_types[ $block_name ] ?? null;
		
		$block_title = $block_name;
		$template_file = 'Unknown';
		
		if ( $block_type ) {
			if ( isset( $block_type->title ) ) {
				$block_title = $block_type->title;
			}
			
			if ( isset( $block_type->render_callback ) && is_callable( $block_type->render_callback ) ) {
				$template_file = 'render_callback()';
			} elseif ( ! empty( $block_type->editor_script ) ) {
				$template_file = 'JS Block';
			}
		}
		
		// Try to determine template file from ACF block registration
		if ( function_exists( 'acf_get_block_types' ) ) {
			$acf_blocks = acf_get_block_types();
			foreach ( $acf_blocks as $acf_block ) {
				if ( $acf_block['name'] === $block_name ) {
					$block_title = $acf_block['title'] ?? $block_name;
					if ( isset( $acf_block['render_template'] ) ) {
						$template_file = basename( $acf_block['render_template'] );
					} elseif ( isset( $acf_block['render_callback'] ) ) {
						$template_file = 'render_callback()';
					}
					break;
				}
			}
		}
		
		// Store block info for frontend display
		global $golden_template_debug_blocks;
		if ( ! isset( $golden_template_debug_blocks ) ) {
			$golden_template_debug_blocks = array();
		}
		
		$golden_template_debug_blocks[] = array(
			'name'          => $block_name,
			'title'         => $block_title,
			'template_file' => $template_file,
		);
		
		return $block_content;
	}
}

/**
 * Theme setup.
 */
function golden_template_setup() {
	// Make theme available for translation.
	load_theme_textdomain( 'golden-template', GOLDEN_TEMPLATE_THEME_DIR . '/languages' );

	// Add default posts and comments RSS feed links to head.
	add_theme_support( 'automatic-feed-links' );

	// Let WordPress manage the document title.
	add_theme_support( 'title-tag' );

	// Enable support for Post Thumbnails on posts and pages.
	add_theme_support( 'post-thumbnails' );

	// Add custom image sizes.
	add_image_size( 'golden-template-featured', 1200, 600, true );
	add_image_size( 'golden-template-thumbnail', 400, 300, true );

	// Register navigation menus.
	register_nav_menus(
		array(
			'primary'   => esc_html__( 'Primary Menu', 'golden-template' ),
			'footer'    => esc_html__( 'Footer Menu', 'golden-template' ),
		)
	);

	// Switch default core markup to output valid HTML5.
	add_theme_support(
		'html5',
		array(
			'search-form',
			'comment-form',
			'comment-list',
			'gallery',
			'caption',
			'style',
			'script',
		)
	);

	// Add theme support for selective refresh for widgets.
	add_theme_support( 'customize-selective-refresh-widgets' );

	// Add support for custom logo.
	add_theme_support(
		'custom-logo',
		array(
			'height'      => 100,
			'width'       => 400,
			'flex-height' => true,
			'flex-width'  => true,
		)
	);

	// Add support for editor styles.
	add_theme_support( 'editor-styles' );
	add_editor_style( 'assets/css/editor-style.css' );

	// Add support for responsive embeds.
	add_theme_support( 'responsive-embeds' );

	// Add support for align wide and full.
	add_theme_support( 'align-wide' );
}
add_action( 'after_setup_theme', 'golden_template_setup' );

/**
 * Add preconnect hints for external fonts
 */
function golden_template_font_preconnect( $urls, $relation_type ) {
	if ( 'preconnect' === $relation_type ) {
		$urls[] = array(
			'href' => 'https://fonts.googleapis.com',
		);
		$urls[] = array(
			'href' => 'https://fonts.gstatic.com',
			'crossorigin' => 'anonymous',
		);
	}
	return $urls;
}
add_filter( 'wp_resource_hints', 'golden_template_font_preconnect', 10, 2 );

/**
 * Enqueue fonts
 */
function golden_template_enqueue_fonts() {
	// Google Fonts (Archivo, Geist, Libre Baskerville)
	wp_enqueue_style(
		'golden-template-google-fonts',
		'https://fonts.googleapis.com/css2?family=Archivo:ital,wght@0,100..900;1,100..900&family=Geist:wght@100..900&family=Libre+Baskerville:ital,wght@0,400;0,700;1,400&display=swap',
		array(),
		null
	);
	
	// Google Fonts (Bricolage Grotesque)
	wp_enqueue_style(
		'golden-template-bricolage-grotesque',
		'https://fonts.googleapis.com/css2?family=Bricolage+Grotesque:opsz,wght@12..96,200..800&display=swap',
		array(),
		null
	);
}
add_action( 'wp_enqueue_scripts', 'golden_template_enqueue_fonts', 5 );

/**
 * Enqueue scripts and styles.
 */
function golden_template_scripts() {


	
	// 1. Main theme styles with variables and base - only load on frontend.
	if ( ! is_admin() ) {
		wp_enqueue_style(
			'golden-template-main',
			GOLDEN_TEMPLATE_THEME_URI . '/assets/css/frontend/main.css',
			array(),
			golden_template_get_asset_version( 'assets/css/frontend/main.css' )
		);
	}

	// 2. Hero section styles - Loads from blocks folder where it compiles
	wp_enqueue_style(
		'golden-template-hero-section',
		GOLDEN_TEMPLATE_THEME_URI . '/blocks/hero-section/hero-section.css',
		array( 'golden-template-main' ), // Depends on main.css, loads after it
		golden_template_get_asset_version( 'blocks/hero-section/hero-section.css' )
	);
	
	// 2. Hero section styles - Loads from blocks folder where it compiles
	wp_enqueue_style(
		'golden-template-hero-section',
		GOLDEN_TEMPLATE_THEME_URI . '/blocks/hero-section/hero-section.css',
		array( 'golden-template-main' ), // Depends on main.css, loads after it
		golden_template_get_asset_version( 'blocks/hero-section/hero-section.css' )
	);
	
	// Slick Carousel
    wp_enqueue_style(
        'slick-carousel',
        'https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.css',
        array(),
        '1.8.1'
    );
    wp_enqueue_style( // Optional theme
        'slick-carousel-theme',
        'https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick-theme.css',
        array('slick-carousel'),
        '1.8.1'
    );
    wp_enqueue_script(
        'slick-carousel',
        'https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.min.js',
        array('jquery'),
        '1.8.1',
        true
    );

	// 12. Projects Listing styles (only on archive pages)
	if ( is_post_type_archive( 'projects' ) || is_page_template( 'archive-projects.php' ) ) {
		wp_enqueue_style(
			'golden-template-projects-listing',
			GOLDEN_TEMPLATE_THEME_URI . '/assets/css/frontend/projects-listing.css',
			array( 'golden-template-main' ),
			golden_template_get_asset_version( 'assets/css/frontend/projects-listing.css' )
		);

		// Projects Listing Script
		wp_enqueue_script(
			'golden-template-projects-filters',
			GOLDEN_TEMPLATE_THEME_URI . '/assets/js/frontend/project-filters.js',
			array( 'jquery' ),
			golden_template_get_asset_version( 'assets/js/frontend/project-filters.js' ),
			true
		);
	}

	// 13. Single Project styles and scripts (only on single project pages)
	if ( is_singular( 'projects' ) ) {
		wp_enqueue_style(
			'golden-template-single-project',
			GOLDEN_TEMPLATE_THEME_URI . '/assets/css/frontend/single-project.css',
			array( 'golden-template-main' ),
			golden_template_get_asset_version( 'assets/css/frontend/single-project.css' )
		);


		// Enqueue Barba.js for single project pages
		if ( ! wp_script_is( 'barba', 'enqueued' ) ) {
			wp_enqueue_script(
				'barba',
				'https://cdn.jsdelivr.net/npm/@barba/core@2.9.7/dist/barba.umd.js',
				array(),
				'2.9.7',
				true
			);
		}
	}
	// Enqueue header/navigation scripts.
	wp_enqueue_script(
		'golden-template-header',
		GOLDEN_TEMPLATE_THEME_URI . '/assets/js/frontend/header.js',
		array(),
		golden_template_get_asset_version( 'assets/js/frontend/header.js' ),
		true
	);

	// Enqueue theme scripts.
	wp_enqueue_script(
		'golden-template-scripts',
		GOLDEN_TEMPLATE_THEME_URI . '/assets/js/frontend/main.js',
		array( 'jquery', 'scrolltrigger' ), // Depend on scrolltrigger (which depends on gsap)
		golden_template_get_asset_version( 'assets/js/frontend/main.js' ),
		true
	);

	// Enqueue BugHerd script
	wp_enqueue_script(
		'bugherd',
		'https://www.bugherd.com/sidebarv2.js?apikey=xbzrkku835zqaxfk21f2yw',
		array(),
		null,
		false
	);
	wp_script_add_data( 'bugherd', 'async', true );

	
	// Localize script for AJAX and other data.
	wp_localize_script(
		'golden-template-scripts',
		'golden-templateData',
		array(
			'ajaxUrl'  => admin_url( 'admin-ajax.php' ),
			'nonce'    => wp_create_nonce( 'golden_template_nonce' ),
			'siteUrl'  => home_url( '/' ),
			'themePath' => GOLDEN_TEMPLATE_THEME_URI,
		)
	);
}
add_action( 'wp_enqueue_scripts', 'golden_template_scripts');

/**
 * Enqueue admin/backend styles.
 */
function golden_template_admin_styles() {
	wp_enqueue_style(
		'golden-template-backend',
		GOLDEN_TEMPLATE_THEME_URI . '/assets/css/admin/backend.css',
		array(),
		file_exists( GOLDEN_TEMPLATE_THEME_DIR . '/assets/css/admin/backend.css' ) 
			? filemtime( GOLDEN_TEMPLATE_THEME_DIR . '/assets/css/admin/backend.css' )
			: GOLDEN_TEMPLATE_VERSION
	);
}
add_action( 'admin_enqueue_scripts', 'golden_template_admin_styles' );

/**
 * Get custom logo or site title.
 */
function golden_template_get_logo() {
	// Check if custom logo is set via theme settings.
	$logo_id = get_option( 'golden_template_logo' );
	
	if ( $logo_id ) {
		$logo_url = wp_get_attachment_url( $logo_id );
		if ( $logo_url ) {
			return sprintf(
				'<a href="%1$s" class="custom-logo-link" rel="home"><img src="%2$s" class="custom-logo" alt="%3$s"></a>',
				esc_url( home_url( '/' ) ),
				esc_url( $logo_url ),
				esc_attr( get_bloginfo( 'name' ) )
			);
		}
	}

	// Fall back to WordPress custom logo.
	if ( has_custom_logo() ) {
		return get_custom_logo();
	}

	// Fall back to site title.
	return sprintf(
		'<h1 class="site-title"><a href="%1$s" rel="home">%2$s</a></h1>',
		esc_url( home_url( '/' ) ),
		esc_html( get_bloginfo( 'name' ) )
	);
}

/**
 * Get placeholder image.
 *
 * @return string Placeholder image URL.
 */
function golden_template_get_placeholder_image() {
	$placeholder_id = get_option( 'golden_template_placeholder_image' );
	
	if ( $placeholder_id ) {
		$placeholder_url = wp_get_attachment_url( $placeholder_id );
		if ( $placeholder_url ) {
			return $placeholder_url;
		}
	}

	// Fall back to a default gray placeholder.
	return get_template_directory_uri() . '/assets/images/placeholder.png';
}

/**
 * Check if current post/page uses ACF components.
 *
 * @return bool True if using ACF components, false otherwise.
 */
function golden_template_uses_acf_components() {
	$content = get_the_content();
	
	// Check if content contains ACF blocks.
	if ( has_block( 'acf/hero-section', $content ) ) {
		return true;
	}
	return false;
}

/**
 * Null-safe wp_kses_post wrapper
 *
 * @param mixed $content Content to sanitize.
 * @return string Sanitized content.
 */
function golden_template_safe_kses_post( $content ) {
	if ( is_null( $content ) || ! is_string( $content ) ) {
		return '';
	}
	return wp_kses_post( $content );
}

/**
 * Allow script tags in all ACF WYSIWYG fields
 *
 * @param mixed  $value    The field value.
 * @param int    $_post_id Post ID (unused but required by ACF filter signature).
 * @param array  $_field   Field array (unused but required by ACF filter signature).
 * @return mixed The field value.
 */
function golden_template_preserve_scripts_in_wysiwyg( $value, $_post_id, $_field ) { // phpcs:ignore VariableAnalysis.CodeAnalysis.VariableAnalysis.UnusedVariable -- Parameters required by ACF filter signature.
	return $value;
}
add_filter( 'acf/update_value/type=wysiwyg', 'golden_template_preserve_scripts_in_wysiwyg', 5, 3 );
add_filter( 'acf/format_value/type=wysiwyg', 'golden_template_preserve_scripts_in_wysiwyg', 5, 3 );
add_filter( 'acf/load_value/type=wysiwyg', 'golden_template_preserve_scripts_in_wysiwyg', 5, 3 );

/**
 * Allow script tags in wp_kses for all WYSIWYG editors.
 */
function golden_template_allow_scripts_in_wysiwyg_kses( $allowed_html, $context ) {
	if ( 'post' === $context ) {
		$allowed_html['script'] = array(
			'src'   => array(),
			'type'  => array(),
			'async' => array(),
			'defer' => array(),
			'id'    => array(),
			'class' => array(),
		);
	}
	return $allowed_html;
}
add_filter( 'wp_kses_allowed_html', 'golden_template_allow_scripts_in_wysiwyg_kses', 10, 2 );

/**
 * Register custom WYSIWYG toolbar for line block (bold, italic, link only)
 */
function golden_template_custom_wysiwyg_toolbars( $toolbars ) {
	// Add minimal toolbar with only bold, italic, and link
	$toolbars['minimal'] = array(
		1 => array(
			'bold',
			'italic',
			'link',
		),
	);

	$toolbars['standard'] = array(
		1 => array(
			'formatselect',
			'bold',
			'italic',
			'underline',
			'link',
			'alignleft',
			'aligncenter',
			'alignright',
		),
	);

	// Format toolbar: same as standard but without alignment options
	$toolbars['format'] = array(
		1 => array(
			'formatselect',
			'bold',
			'italic',
			'underline',
			'link',
		),
	);

	return $toolbars;
}
add_filter( 'acf/fields/wysiwyg/toolbars', 'golden_template_custom_wysiwyg_toolbars' );

/**
 * Include additional theme files.
 */
require_once GOLDEN_TEMPLATE_THEME_DIR . '/inc/cache-optimization.php';
require_once GOLDEN_TEMPLATE_THEME_DIR . '/inc/template-functions.php';
require_once GOLDEN_TEMPLATE_THEME_DIR . '/inc/class-nav-walker.php';
require_once GOLDEN_TEMPLATE_THEME_DIR . '/inc/asset-loading.php';
require_once GOLDEN_TEMPLATE_THEME_DIR . '/inc/ajax-handlers.php';

/**
 * Load ACF configurations.
 */
function golden_template_load_acf_config() {
	if ( function_exists( 'acf_add_options_page' ) ) {
		require_once GOLDEN_TEMPLATE_THEME_DIR . '/inc/acf-config.php';
	}
}
add_action( 'init', 'golden_template_load_acf_config', 5 );

/**
 * Load editor customization.
 */
require_once GOLDEN_TEMPLATE_THEME_DIR . '/inc/editor-customization.php';

/**
 * Load ACF Blocks system.
 */
require_once GOLDEN_TEMPLATE_THEME_DIR . '/inc/blocks/block-helpers.php';
require_once GOLDEN_TEMPLATE_THEME_DIR . '/inc/blocks/block-registration.php';

/**
 * Customizer settings
 */

/**
 * Load individual block field configurations.
 */
require_once GOLDEN_TEMPLATE_THEME_DIR . '/blocks/hero-section/fields.php';
/**
 * Custom Post Types
 */
require_once GOLDEN_TEMPLATE_THEME_DIR . '/inc/post-types/projects.php';

/**
 * Custom Taxonomies
 */
require_once GOLDEN_TEMPLATE_THEME_DIR . '/inc/taxonomies/projects-taxonomies.php';

/**
 * CPT Fields
 */
require_once GOLDEN_TEMPLATE_THEME_DIR . '/inc/fields/projects-cpt-fields.php';


/**
 * Security enhancements
 */

// Disable XML-RPC completely for security
add_filter( 'xmlrpc_enabled', '__return_false' );

// Block XML-RPC requests at the PHP level as an additional security layer
add_action( 'init', 'golden_template_block_xmlrpc_requests' );
function golden_template_block_xmlrpc_requests() {
	// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- Checking REQUEST_URI for security blocking
	if ( isset( $_SERVER['REQUEST_URI'] ) && false !== strpos( $_SERVER['REQUEST_URI'], 'xmlrpc.php' ) ) {
		http_response_code( 403 );
		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Intentional plain text security response
		die( 'XML-RPC is disabled for security.' );
	}
}

/**
 * Restrict user enumeration via REST API
 */
add_filter( 'rest_endpoints', 'golden_template_restrict_rest_users' );
function golden_template_restrict_rest_users( $endpoints ) {
	// Completely remove the users endpoint for non-authenticated users
	if ( ! is_user_logged_in() ) {
		if ( isset( $endpoints['/wp/v2/users'] ) ) {
			unset( $endpoints['/wp/v2/users'] );
		}
		if ( isset( $endpoints['/wp/v2/users/(?P<id>[\d]+)'] ) ) {
			unset( $endpoints['/wp/v2/users/(?P<id>[\d]+)'] );
		}
	}
	return $endpoints;
}

/**
 * Secure REST API endpoints - Block unauthorized access
 */
add_filter( 'rest_authentication_errors', 'golden_template_secure_rest_api' );
function golden_template_secure_rest_api( $result ) {
	// Don't override existing authentication errors
	if ( true === $result || is_wp_error( $result ) ) {
		return $result;
	}

	// Allow all requests if user is logged in
	if ( is_user_logged_in() ) {
		return $result;
	}

	// Get the current REST route
	$route = isset( $GLOBALS['wp']->query_vars['rest_route'] ) ? $GLOBALS['wp']->query_vars['rest_route'] : '';
	
	// If no route yet, try to get from REQUEST_URI
	if ( empty( $route ) ) {
		// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized, WordPress.Security.ValidatedSanitizedInput.MissingUnescapeString -- Checking REQUEST_URI for route matching
		$request_uri = isset( $_SERVER['REQUEST_URI'] ) ? $_SERVER['REQUEST_URI'] : '';
		if ( strpos( $request_uri, '/wp-json/' ) !== false ) {
			$route = substr( $request_uri, strpos( $request_uri, '/wp-json/' ) + 8 );
			$route = strtok( $route, '?' ); // Remove query string
		}
	}

	// List of endpoints that should be completely blocked for non-authenticated users
	$blocked_endpoints = array(
		'/wp/v2/users',
		'/wp/v2/settings',
		'/wp/v2/themes',
		'/wp/v2/plugins',
		'/wp/v2/block-directory',
	);

	foreach ( $blocked_endpoints as $endpoint ) {
		if ( strpos( $route, $endpoint ) !== false ) {
			return new WP_Error(
				'rest_forbidden',
				'Access to this endpoint is restricted.',
				array( 'status' => 403 )
			);
		}
	}

	return $result;
}

/**
 * Make categories and tags read-only for non-authenticated users
 */
add_filter( 'rest_pre_dispatch', 'golden_template_restrict_rest_write_access', 10, 3 );
function golden_template_restrict_rest_write_access( $result, $server, $request ) {
	// Allow all requests if user is logged in
	if ( is_user_logged_in() ) {
		return $result;
	}


	$route = $request->get_route();
	$method = $request->get_method();

	// List of endpoints that should be read-only (GET only) for non-authenticated users
	$readonly_endpoints = array(
		'/wp/v2/categories',
		'/wp/v2/tags',
		'/wp/v2/media',
	);

	// Check if this is a write operation (POST, PUT, PATCH, DELETE) to a read-only endpoint
	if ( in_array( $method, array( 'POST', 'PUT', 'PATCH', 'DELETE' ), true ) ) {
		foreach ( $readonly_endpoints as $endpoint ) {
			if ( strpos( $route, $endpoint ) !== false ) {
				return new WP_Error(
					'rest_forbidden',
					'You must be logged in to modify this resource.',
					array( 'status' => 403 )
				);
			}
		}
	}

	return $result;
}