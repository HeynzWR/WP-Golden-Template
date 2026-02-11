<?php
/**
 * Plugin Name: Golden Template Core
 * Plugin URI: https://golden-template.com
 * Description: Core functionality for Golden Template theme including update management, plugin dependency checks, and theme settings.
 * Version: 1.0.0
 * Author: Golden Template
 * Author URI: https://golden-template.com
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: golden-template-core
 *
 * @package GoldenTemplate_Core
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Define plugin constants.
define( 'GOLDEN_TEMPLATE_CORE_VERSION', '1.0.0' );
define( 'GOLDEN_TEMPLATE_CORE_FILE', __FILE__ );
define( 'GOLDEN_TEMPLATE_CORE_PATH', plugin_dir_path( __FILE__ ) );
define( 'GOLDEN_TEMPLATE_CORE_URL', plugin_dir_url( __FILE__ ) );

/**
 * Autoloader for Golden Template Core classes.
 *
 * @param string $class_name The class name to load.
 */
function golden_template_core_autoload( $class_name ) {
	// Check if the class uses our namespace.
	if ( strpos( $class_name, 'GoldenTemplate_Core_' ) !== 0 ) {
		return;
	}

	// Convert class name to file name.
	$class_file = 'class-' . strtolower( str_replace( '_', '-', str_replace( 'GoldenTemplate_Core_', '', $class_name ) ) ) . '.php';
	$file_path  = GOLDEN_TEMPLATE_CORE_PATH . 'includes/' . $class_file;

	// Include the file if it exists.
	if ( file_exists( $file_path ) ) {
		require_once $file_path;
	}
}

spl_autoload_register( 'golden_template_core_autoload' );

/**
 * Main Golden Template Core class.
 */
class GoldenTemplate_Core {

	/**
	 * Single instance of the class.
	 *
	 * @var GoldenTemplate_Core
	 */
	private static $instance = null;

	/**
	 * Plugin checker instance.
	 *
	 * @var GoldenTemplate_Core_Plugin_Checker
	 */
	public $plugin_checker;

	/**
	 * Update manager instance.
	 *
	 * @var GoldenTemplate_Core_Update_Manager
	 */
	public $update_manager;

	/**
	 * Settings instance.
	 *
	 * @var GoldenTemplate_Core_Settings
	 */
	public $settings;

	/**
	 * Logger instance.
	 *
	 * @var GoldenTemplate_Core_Logger
	 */
	public $logger;

	/**
	 * Branding instance.
	 *
	 * @var GoldenTemplate_Core_Branding
	 */
	public $branding;

	/**
	 * Get the single instance of the class.
	 *
	 * @return GoldenTemplate_Core
	 */
	public static function get_instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * Constructor - Initialize the plugin.
	 */
	private function __construct() {
		$this->init();
	}

	/**
	 * Initialize the plugin.
	 */
	private function init() {
		// Hook into WordPress.
		add_action( 'init', array( $this, 'load_textdomain' ), 1 );
		add_action( 'init', array( $this, 'init_components' ) );
		
		// Enable SVG uploads for logos.
		add_filter( 'upload_mimes', array( $this, 'enable_svg_uploads' ) );
		add_filter( 'wp_check_filetype_and_ext', array( $this, 'fix_svg_mime_type' ), 10, 5 );
	}

	/**
	 * Load plugin textdomain for translations.
	 */
	public function load_textdomain() {
		load_plugin_textdomain(
			'golden-template-core',
			false,
			dirname( plugin_basename( GOLDEN_TEMPLATE_CORE_FILE ) ) . '/languages'
		);
		
		// Initialize core components after textdomain is loaded.
		$this->logger         = new GoldenTemplate_Core_Logger();
		$this->branding       = new GoldenTemplate_Core_Branding();
		$this->plugin_checker = new GoldenTemplate_Core_Plugin_Checker();
		$this->update_manager = new GoldenTemplate_Core_Update_Manager();
		$this->settings       = new GoldenTemplate_Core_Settings();
	}

	/**
	 * Initialize plugin components.
	 */
	public function init_components() {
		// Log plugin initialization.
		$this->logger->log( 'Golden Template Core initialized', 'info' );
	}

	/**
	 * Enable SVG file uploads for logos.
	 *
	 * @param array $mimes Allowed mime types.
	 * @return array
	 */
	public function enable_svg_uploads( $mimes ) {
		// Add SVG mime type.
		$mimes['svg']  = 'image/svg+xml';
		$mimes['svgz'] = 'image/svg+xml';
		
		return $mimes;
	}

	/**
	 * Fix SVG mime type detection.
	 *
	 * @param array  $data File data.
	 * @param string $file File path.
	 * @param string $filename File name.
	 * @param array  $mimes Allowed mime types.
	 * @param string $real_mime Real mime type.
	 * @return array
	 */
	public function fix_svg_mime_type( $data, $file, $filename, $mimes, $real_mime = '' ) {
		// If it's an SVG file.
		if ( ! empty( $data['ext'] ) && 'svg' === $data['ext'] ) {
			$data['type'] = 'image/svg+xml';
			$data['ext']  = 'svg';
		}
		
		// Check file extension.
		$extension = pathinfo( $filename, PATHINFO_EXTENSION );
		if ( 'svg' === $extension || 'svgz' === $extension ) {
			$data['ext']  = $extension;
			$data['type'] = 'image/svg+xml';
		}
		
		return $data;
	}
}

/**
 * Get the main instance of Golden Template Core.
 *
 * @return GoldenTemplate_Core
 */
function golden_template_core() {
	return GoldenTemplate_Core::get_instance();
}

// Initialize the plugin.
golden_template_core();
