<?php
/**
 * Update Manager Class
 *
 * Handles theme and plugin updates, including automatic security updates.
 *
 * @package JLBPartners_Core
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * JLB Partners Core Update Manager class.
 */
class JLBPartners_Core_Update_Manager {

	/**
	 * API endpoint for checking updates.
	 *
	 * @var string
	 */
	private $api_endpoint;

	/**
	 * Constructor.
	 */
	public function __construct() {
		$this->api_endpoint = get_option( 'jlbpartners_update_api_endpoint', '' );

		// Hook into WordPress update system.
		add_filter( 'site_transient_update_themes', array( $this, 'check_theme_updates' ), 10, 1 );

		// Schedule automatic update checks.
		add_action( 'jlbpartners_check_updates', array( $this, 'check_for_updates' ) );
		if ( ! wp_next_scheduled( 'jlbpartners_check_updates' ) ) {
			wp_schedule_event( time(), 'hourly', 'jlbpartners_check_updates' );
		}
	}

	/**
	 * Get current theme version.
	 *
	 * @return string Theme version.
	 */
	public function get_current_theme_version() {
		$theme = wp_get_theme( 'jlbpartners' );
		return $theme->get( 'Version' );
	}

	/**
	 * Check for updates from API.
	 *
	 * @return array|bool Update data or false if none available.
	 */
	public function check_for_updates() {
		// If no API endpoint is configured, return false.
		if ( empty( $this->api_endpoint ) ) {
			return false;
		}

		$current_version = $this->get_current_theme_version();

		// Prepare request data.
		$request_data = array(
			'theme_version'  => $current_version,
			'site_url'       => home_url(),
			'wp_version'     => get_bloginfo( 'version' ),
			'php_version'    => phpversion(),
			'active_plugins' => get_option( 'active_plugins', array() ),
		);

		// Make API request.
		$response = wp_remote_post(
			$this->api_endpoint,
			array(
				'body'    => wp_json_encode( $request_data ),
				'headers' => array(
					'Content-Type' => 'application/json',
				),
				'timeout' => 10,
			)
		);

		// Check for errors.
		if ( is_wp_error( $response ) ) {
			$logger = jlbpartners_core()->logger;
			$logger->log(
				'Failed to check for updates: ' . $response->get_error_message(),
				'error',
				'update'
			);
			return false;
		}

		// Parse response.
		$body = wp_remote_retrieve_body( $response );
		$data = json_decode( $body, true );

		if ( empty( $data ) ) {
			return false;
		}

		// Store update data.
		set_transient( 'jlbpartners_available_updates', $data, 12 * HOUR_IN_SECONDS );

		// Log update check.
		$logger = jlbpartners_core()->logger;
		$logger->log(
			'Checked for updates successfully',
			'info',
			'update',
			$data
		);

		return $data;
	}

	/**
	 * Check theme updates (hook into WordPress update system).
	 *
	 * @param object $transient Update transient.
	 * @return object Modified transient.
	 */
	public function check_theme_updates( $transient ) {
		if ( empty( $transient->checked ) ) {
			return $transient;
		}

		// Get available updates.
		$updates = get_transient( 'jlbpartners_available_updates' );

		if ( empty( $updates ) || empty( $updates['theme_update'] ) ) {
			return $transient;
		}

		$theme_update = $updates['theme_update'];

		// Add update to transient.
		if ( ! empty( $theme_update['version'] ) && ! empty( $theme_update['download_url'] ) ) {
			$transient->response['jlbpartners'] = array(
				'theme'       => 'jlbpartners',
				'new_version' => $theme_update['version'],
				'url'         => home_url(),
				'package'     => $theme_update['download_url'],
			);
		}

		return $transient;
	}

	/**
	 * Get update history.
	 *
	 * @param int $limit Number of updates to retrieve.
	 * @return array Update history.
	 */
	public function get_update_history( $limit = 50 ) {
		$logger = jlbpartners_core()->logger;
		return $logger->get_logs(
			array(
				'category' => 'update',
				'limit'    => $limit,
			)
		);
	}
}
