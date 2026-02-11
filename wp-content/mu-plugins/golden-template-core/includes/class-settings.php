<?php
/**
 * Settings Class
 *
 * Handles theme settings page in WordPress admin.
 *
 * @package GoldenTemplate_Core
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Golden Template Core Settings class.
 */
class GoldenTemplate_Core_Settings {

	/**
	 * Settings page slug.
	 *
	 * @var string
	 */
	private $page_slug = 'golden-template-settings';

	/**
	 * Constructor.
	 */
	public function __construct() {
		add_action( 'admin_menu', array( $this, 'add_settings_page' ) );
		add_action( 'admin_init', array( $this, 'register_settings' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_assets' ) );

		// Handle logo upload via AJAX.
		add_action( 'wp_ajax_golden_template_upload_logo', array( $this, 'handle_logo_upload' ) );
		add_action( 'wp_ajax_golden_template_upload_placeholder', array( $this, 'handle_placeholder_upload' ) );

		// Register copyright shortcode.
		add_shortcode( 'golden_template_copyright', array( $this, 'copyright_shortcode' ) );
	}

	/**
	 * Add settings page to admin menu.
	 */
	public function add_settings_page() {
		add_menu_page(
			__( 'Golden Template', 'golden-template-core' ),
			__( 'Golden Template', 'golden-template-core' ),
			'manage_options',
			$this->page_slug,
			array( $this, 'render_settings_page' ),
			'dashicons-admin-customizer',
			3
		);

		// Add Project Listing settings as a submenu under Projects CPT with slug "projects"
		add_submenu_page(
			'edit.php?post_type=projects',
			__( 'Project Listing Settings', 'golden-template-core' ),
			__( 'Listing Settings', 'golden-template-core' ),
			'manage_options',
			'projects',
			array( $this, 'render_projects_settings_page' )
		);
	}

	/**
	 * Register settings.
	 */
	public function register_settings() {
		// Branding settings - logos and images.
		register_setting(
			'golden_template_branding',
			'golden_template_logo',
			array(
				'type'              => 'integer',
				'sanitize_callback' => 'absint',
				'default'           => 0,
			)
		);
		register_setting(
			'golden_template_branding',
			'golden_template_logo_desktop',
			array(
				'type'              => 'integer',
				'sanitize_callback' => 'absint',
				'default'           => 0,
			)
		);
		register_setting(
			'golden_template_branding',
			'golden_template_logo_mobile',
			array(
				'type'              => 'integer',
				'sanitize_callback' => 'absint',
				'default'           => 0,
			)
		);
		register_setting(
			'golden_template_branding',
			'golden_template_logo_desktop_dark',
			array(
				'type'              => 'integer',
				'sanitize_callback' => 'absint',
				'default'           => 0,
			)
		);
		register_setting(
			'golden_template_branding',
			'golden_template_logo_mobile_dark',
			array(
				'type'              => 'integer',
				'sanitize_callback' => 'absint',
				'default'           => 0,
			)
		);
		register_setting(
			'golden_template_branding',
			'golden_template_placeholder_image',
			array(
				'type'              => 'integer',
				'sanitize_callback' => 'absint',
				'default'           => 0,
			)
		);

		// Footer settings.
		register_setting( 'golden_template_footer', 'golden_template_footer_address', array( 'type' => 'string', 'sanitize_callback' => 'wp_kses_post', 'default' => '' ) );

		// Project listing settings.
		register_setting(
			'golden_template_projects',
			'golden_template_projects_page_title',
			array(
				'type'              => 'string',
				'sanitize_callback' => 'sanitize_text_field',
				'default'           => 'All Projects',
			)
		);
		register_setting(
			'golden_template_projects',
			'golden_template_projects_per_page',
			array(
				'type'              => 'integer',
				'sanitize_callback' => 'absint',
				'default'           => 2,
			)
		);

		// Update settings.
		register_setting( 'golden_template_updates', 'golden_template_update_api_endpoint' );
	}

	/**
	 * Enqueue admin assets.
	 *
	 * @param string $hook Current admin page hook.
	 */
	public function enqueue_admin_assets( $hook ) {
		// Load on our settings page and projects page.
		$settings_hook = 'toplevel_page_' . $this->page_slug;
		$projects_hook = 'projects_page_projects';
		
		if ( $settings_hook !== $hook && $projects_hook !== $hook ) {
			return;
		}

		// Enqueue WordPress core styles.
		wp_enqueue_style( 'dashicons' );
		
		// Enqueue media uploader with all dependencies.
		wp_enqueue_media();

		// Enqueue custom admin script with proper dependencies including media.
		wp_enqueue_script(
			'golden-template-admin',
			GOLDEN_TEMPLATE_CORE_URL . 'assets/js/admin.js',
			array( 'jquery', 'media-upload', 'media-views', 'media-editor' ),
			GOLDEN_TEMPLATE_CORE_VERSION,
			true
		);

		// Localize script.
		wp_localize_script(
			'golden-template-admin',
			'golden-templateAdmin',
			array(
				'ajaxUrl' => admin_url( 'admin-ajax.php' ),
				'nonce'   => wp_create_nonce( 'golden_template_admin_nonce' ),
			)
		);
		
		// Add plugin URL constant for JavaScript.
		wp_add_inline_script(
			'golden-template-admin',
			'const GOLDEN_TEMPLATE_CORE_URL = "' . esc_js( GOLDEN_TEMPLATE_CORE_URL ) . '";',
			'before'
		);

		// Enqueue custom admin styles.
		wp_enqueue_style(
			'golden-template-admin',
			GOLDEN_TEMPLATE_CORE_URL . 'assets/css/admin.css',
			array(),
			GOLDEN_TEMPLATE_CORE_VERSION
		);
		
		// Add inline styles as fallback.
		$inline_css = file_get_contents( GOLDEN_TEMPLATE_CORE_PATH . 'assets/css/admin.css' );
		wp_add_inline_style( 'dashicons', $inline_css );
		
		// Add inline script to verify form submission.
		$inline_js = "
		console.log('Golden Template Admin: Scripts loaded');
		jQuery(document).ready(function($) {
			console.log('Golden Template Admin: jQuery ready');
			console.log('Golden Template Admin: Media uploader available:', typeof wp !== 'undefined' && typeof wp.media !== 'undefined');
		});
		";
		wp_add_inline_script( 'golden-template-admin', $inline_js );
	}

	/**
	 * Render settings page.
	 */
	public function render_settings_page() {
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		// Get current branding values.
		$logo_id         = get_option( 'golden_template_logo', 0 );
		$logo_url        = $logo_id ? wp_get_attachment_url( $logo_id ) : '';
		$placeholder_id  = get_option( 'golden_template_placeholder_image', 0 );
		$placeholder_url = $placeholder_id ? wp_get_attachment_url( $placeholder_id ) : '';
		
		// Update settings.
		$api_endpoint = get_option( 'golden_template_update_api_endpoint', '' );

		// Get system info.
		$update_manager  = golden_template_core()->update_manager;
		$branding        = golden_template_core()->branding;
		$plugin_checker  = golden_template_core()->plugin_checker;
		$update_history  = $update_manager->get_update_history( 20 );
		$theme_version   = $update_manager->get_current_theme_version();
		$missing_plugins = $plugin_checker->get_missing_plugins();

		// Load settings page template.
		require GOLDEN_TEMPLATE_CORE_PATH . 'templates/settings-page.php';
	}

	/**
	 * Handle logo upload via AJAX.
	 */
	public function handle_logo_upload() {
		check_ajax_referer( 'golden_template_admin_nonce', 'nonce' );

		if ( ! current_user_can( 'manage_options' ) ) {
			wp_send_json_error( array( 'message' => __( 'Insufficient permissions', 'golden-template-core' ) ) );
		}

		$attachment_id = isset( $_POST['attachment_id'] ) ? absint( $_POST['attachment_id'] ) : 0;

		if ( $attachment_id ) {
			update_option( 'golden_template_logo', $attachment_id );
			wp_send_json_success( array( 'message' => __( 'Logo updated', 'golden-template-core' ) ) );
		}

		wp_send_json_error( array( 'message' => __( 'Invalid attachment ID', 'golden-template-core' ) ) );
	}

	/**
	 * Handle placeholder upload via AJAX.
	 */
	public function handle_placeholder_upload() {
		check_ajax_referer( 'golden_template_admin_nonce', 'nonce' );

		if ( ! current_user_can( 'manage_options' ) ) {
			wp_send_json_error( array( 'message' => __( 'Insufficient permissions', 'golden-template-core' ) ) );
		}

		$attachment_id = isset( $_POST['attachment_id'] ) ? absint( $_POST['attachment_id'] ) : 0;

		if ( $attachment_id ) {
			update_option( 'golden_template_placeholder_image', $attachment_id );
			wp_send_json_success( array( 'message' => __( 'Placeholder image updated', 'golden-template-core' ) ) );
		}

		wp_send_json_error( array( 'message' => __( 'Invalid attachment ID', 'golden-template-core' ) ) );
	}

	/**
	 * Copyright shortcode handler.
	 * 
	 * Usage: [golden_template_copyright format="desktop"] or [golden_template_copyright format="mobile"]
	 * 
	 * @param array $atts Shortcode attributes.
	 * @return string Copyright text with auto-updated year.
	 */
	public function copyright_shortcode( $atts ) {
		$atts = shortcode_atts(
			array(
				'format' => 'desktop', // 'desktop' or 'mobile'
			),
			$atts,
			'golden_template_copyright'
		);

		$current_year = date( 'Y' );

		// Format based on desktop or mobile.
		if ( 'mobile' === $atts['format'] ) {
			$copyright = '&copy; ' . $current_year . ' Golden Template. <br>All rights reserved.';
		} else {
			// Desktop format (default).
			$copyright = '&copy; Copyright ' . $current_year;
		}

		return wp_kses_post( $copyright );
	}

	/**
	 * Render projects settings page.
	 */
	public function render_projects_settings_page() {
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		// Load projects settings page template.
		require GOLDEN_TEMPLATE_CORE_PATH . 'templates/projects-settings-page.php';
	}

}
