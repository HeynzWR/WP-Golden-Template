<?php
/**
 * Editor Customization
 *
 * Customizes the WordPress block editor based on post type.
 *
 * @package GoldenTemplate
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Filter allowed block types based on post type
 */
function golden_template_allowed_block_types( $allowed_blocks, $editor_context ) {
	$post_type = '';
	$post_id = 0;
	
	if ( ! empty( $editor_context->post ) ) {
		$post_type = $editor_context->post->post_type;
		$post_id = $editor_context->post->ID;
	}
	
	// For pages: check if it's a privacy policy page.
	if ( 'page' === $post_type ) {
		// Check if this is the privacy policy page.
		$privacy_policy_id = get_option( 'wp_page_for_privacy_policy', 0 );
		$is_privacy_page = ( $privacy_policy_id && $post_id === (int) $privacy_policy_id );
		
		// For privacy policy pages: allow only native WordPress blocks (disable ACF/StudioSwift components).
		if ( $is_privacy_page ) {
			$block_registry = WP_Block_Type_Registry::get_instance();
			$all_blocks = $block_registry->get_all_registered();
			
			$allowed_blocks = array();
			foreach ( $all_blocks as $block_name => $block_type ) {
				if ( strpos( $block_name, 'acf/' ) !== 0 ) {
					$allowed_blocks[] = $block_name;
				}
			}
			return $allowed_blocks;
		}
		
		// For other pages: restrict to only GoldenTemplate blocks.
		return array(
			'acf/hero-section',
			'acf/test-hero-block',
			'acf/about-section',
			'acf/line-block',
			'acf/featured-section',
			'acf/service-slider',
			'acf/title-block',
			'acf/milestones',
			'acf/accordion',
			'acf/map-block',
			'acf/image-block',
			'acf/location-cards',
			'acf/filter-cards',
			'acf/rich-text-editor'
		);
	}
	
	// For posts and custom post types: allow only native WordPress blocks (excluding ACF and theme blocks).
	if ( true === $allowed_blocks ) {
		$block_registry = WP_Block_Type_Registry::get_instance();
		$all_blocks = $block_registry->get_all_registered();
		
		$allowed_blocks = array();
		foreach ( $all_blocks as $block_name => $block_type ) {
			// Exclude ACF blocks
			if ( strpos( $block_name, 'acf/' ) === 0 ) {
				continue;
			}
			
			// Exclude blocks from the "theme" category
			if ( isset( $block_type->category ) && 'theme' === $block_type->category ) {
				continue;
			}
			
			$allowed_blocks[] = $block_name;
		}
	} elseif ( is_array( $allowed_blocks ) ) {
		$block_registry = WP_Block_Type_Registry::get_instance();
		$all_blocks = $block_registry->get_all_registered();
		
		$allowed_blocks = array_filter(
			$allowed_blocks,
			function( $block_name ) use ( $all_blocks ) {
				// Exclude ACF blocks
				if ( strpos( $block_name, 'acf/' ) === 0 ) {
					return false;
				}
				
				// Exclude blocks from the "theme" category
				if ( isset( $all_blocks[ $block_name ] ) ) {
					$block_type = $all_blocks[ $block_name ];
					if ( isset( $block_type->category ) && 'theme' === $block_type->category ) {
						return false;
					}
				}
				
				return true;
			}
		);
	}
	
	return $allowed_blocks;
}
add_filter( 'allowed_block_types_all', 'golden_template_allowed_block_types', 10, 2 );

/**
 * Customize block editor settings
 */
function golden_template_editor_settings( $settings ) {
	$settings['__experimentalBlockPatterns'] = array();
	$settings['__experimentalBlockPatternCategories'] = array();
	$settings['__experimentalPreferPatternsOnRoot'] = false;
	
	return $settings;
}
add_filter( 'block_editor_settings_all', 'golden_template_editor_settings', 10, 1 );

/**
 * Enqueue custom editor scripts
 */
function golden_template_custom_editor_scripts() {
	wp_enqueue_script(
		'golden-template-editor-customization',
		get_template_directory_uri() . '/assets/js/admin/editor-customization.js',
		array( 'wp-blocks', 'wp-dom-ready', 'wp-edit-post' ),
		golden_template_get_asset_version( 'assets/js/admin/editor-customization.js' ),
		true
	);

	wp_enqueue_style(
		'golden-template-editor-customization',
		get_template_directory_uri() . '/assets/css/admin/editor-customization.css',
		array(),
		golden_template_get_asset_version( 'assets/css/admin/editor-customization.css' )
	);
}
add_action( 'enqueue_block_editor_assets', 'golden_template_custom_editor_scripts' );

/**
 * Add custom body class to editor
 */
function golden_template_editor_body_class( $classes ) {
	$classes .= ' golden-template-editor';
	return $classes;
}
add_filter( 'admin_body_class', 'golden_template_editor_body_class' );

/**
 * Add editor help text based on post type
 */
function golden_template_editor_notices() {
	$screen = get_current_screen();
	
	if ( ! $screen || ( 'post' !== $screen->base && 'page' !== $screen->base ) ) {
		return;
	}
	
	global $post;
	$post_type = get_post_type( $post );
	
	if ( 'page' === $post_type ) {
		// Check if this is the privacy policy page.
		$privacy_policy_id = get_option( 'wp_page_for_privacy_policy', 0 );
		$is_privacy_page = ( $privacy_policy_id && $post->ID === (int) $privacy_policy_id );
		
		if ( $is_privacy_page ) {
			$storage_key = 'golden_template_privacy_editor_intro_seen';
			$message = 'Privacy Policy page: You can use all native WordPress blocks. Custom components are disabled on this page.';
		} else {
			$storage_key = 'golden_template_page_editor_intro_seen';
			$message = 'Welcome to JLB Partners! Click the + button to add components to your page.';
		}
	} else {
		$storage_key = 'golden_template_post_editor_intro_seen';
		$message = 'You can use all native WordPress blocks in posts. Custom components are only available on pages.';
	}
	?>
	<script>
		if (typeof wp !== 'undefined' && wp.domReady) {
			wp.domReady(function() {
				if (!localStorage.getItem('<?php echo esc_js( $storage_key ); ?>')) {
					wp.data.dispatch('core/notices').createInfoNotice(
						'<?php echo esc_js( $message ); ?>',
						{
							isDismissible: true,
							type: 'snackbar',
							actions: []
						}
					);
					localStorage.setItem('<?php echo esc_js( $storage_key ); ?>', 'true');
				}
			});
		}
	</script>
	<?php
}
add_action( 'admin_footer', 'golden_template_editor_notices' );

/**
 * Remove unwanted editor panels based on post type
 */
function golden_template_remove_editor_panels() {
	$screen = get_current_screen();
	
	if ( ! $screen || ( 'post' !== $screen->base && 'page' !== $screen->base ) ) {
		return;
	}
	
	global $post;
	$post_type = get_post_type( $post );
	?>
	<script>
		if (typeof wp !== 'undefined' && wp.domReady) {
			wp.domReady(function() {
				<?php if ( 'page' === $post_type ) : ?>
				wp.data.dispatch('core/edit-post').removeEditorPanel('discussion-panel');
				<?php endif; ?>
			});
		}
	</script>
	<?php
}
add_action( 'admin_footer', 'golden_template_remove_editor_panels' );

/**
 * Customize block inserter search
 */
function golden_template_block_inserter_help() {
	?>
	<style>
		.edit-post-header-toolbar__inserter-toggle,
		.edit-site-header-edit-mode__inserter-toggle {
			background: var(--golden-template-primary, #00a400) !important;
			color: white !important;
			border-radius: 6px;
			padding: 8px 16px !important;
			font-weight: 600;
		}
		
		.edit-post-header-toolbar__inserter-toggle:hover,
		.edit-site-header-edit-mode__inserter-toggle:hover {
			background: var(--golden-template-primary-dark, #004500) !important;
		}
		
		.block-editor-inserter__panel-title {
			font-size: 14px;
			font-weight: 600;
			color: #1d2327;
		}
		
		.block-editor-block-types-list__item[data-id^="acf/"] .block-editor-block-icon {
			color: var(--golden-template-primary, #00a400);
		}
		
		.block-editor-block-types-list__item[data-id^="acf/"]:hover {
			background: #f0f9f5;
		}
	</style>
	<?php
}
add_action( 'admin_head', 'golden_template_block_inserter_help' );
