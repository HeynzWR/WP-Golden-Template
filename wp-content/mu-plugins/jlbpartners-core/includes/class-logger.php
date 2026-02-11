<?php
/**
 * Logger Class
 *
 * Handles logging for theme updates and system events.
 *
 * @package JLBPartners_Core
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * JLB Partners Core Logger class.
 */
class JLBPartners_Core_Logger {

	/**
	 * Log table name.
	 *
	 * @var string
	 */
	private $table_name;

	/**
	 * Constructor.
	 */
	public function __construct() {
		global $wpdb;
		$this->table_name = $wpdb->prefix . 'jlbpartners_logs';

		// Create table if it doesn't exist.
		add_action( 'init', array( $this, 'create_table' ) );
	}

	/**
	 * Create the logs table.
	 */
	public function create_table() {
		global $wpdb;

		$charset_collate = $wpdb->get_charset_collate();

		$sql = "CREATE TABLE IF NOT EXISTS {$this->table_name} (
			id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
			timestamp datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
			type varchar(50) NOT NULL,
			category varchar(50) NOT NULL,
			message text NOT NULL,
			details longtext,
			PRIMARY KEY (id),
			KEY type (type),
			KEY category (category),
			KEY timestamp (timestamp)
		) $charset_collate;";

		require_once ABSPATH . 'wp-admin/includes/upgrade.php';
		dbDelta( $sql );
	}

	/**
	 * Log a message.
	 *
	 * @param string $message  The log message.
	 * @param string $type     Log type (info, warning, error, security).
	 * @param string $category Log category (update, plugin, theme, system).
	 * @param array  $details  Additional details to log.
	 * @return bool True on success, false on failure.
	 */
	public function log( $message, $type = 'info', $category = 'system', $details = array() ) {
		global $wpdb;

		$allowed_types      = array( 'info', 'warning', 'error', 'security' );
		$allowed_categories = array( 'update', 'plugin', 'theme', 'system' );

		// Validate type and category.
		if ( ! in_array( $type, $allowed_types, true ) ) {
			$type = 'info';
		}

		if ( ! in_array( $category, $allowed_categories, true ) ) {
			$category = 'system';
		}

		// Sanitize message.
		$message = sanitize_text_field( $message );

		// Prepare details as JSON.
		$details_json = ! empty( $details ) ? wp_json_encode( $details ) : null;

		// Insert log entry.
		$result = $wpdb->insert(
			$this->table_name,
			array(
				'type'     => $type,
				'category' => $category,
				'message'  => $message,
				'details'  => $details_json,
			),
			array(
				'%s',
				'%s',
				'%s',
				'%s',
			)
		);

		return false !== $result;
	}

	/**
	 * Get logs based on filters.
	 *
	 * @param array $args Query arguments.
	 * @return array Array of log entries.
	 */
	public function get_logs( $args = array() ) {
		global $wpdb;

		$defaults = array(
			'type'     => '',
			'category' => '',
			'limit'    => 100,
			'offset'   => 0,
			'orderby'  => 'timestamp',
			'order'    => 'DESC',
		);

		$args = wp_parse_args( $args, $defaults );

		// Build WHERE clause.
		$where = array( '1=1' );

		if ( ! empty( $args['type'] ) ) {
			$where[] = $wpdb->prepare( 'type = %s', sanitize_text_field( $args['type'] ) );
		}

		if ( ! empty( $args['category'] ) ) {
			$where[] = $wpdb->prepare( 'category = %s', sanitize_text_field( $args['category'] ) );
		}

		$where_clause = implode( ' AND ', $where );

		// Sanitize orderby and order.
		$allowed_orderby = array( 'id', 'timestamp', 'type', 'category' );
		$orderby         = in_array( $args['orderby'], $allowed_orderby, true ) ? $args['orderby'] : 'timestamp';
		$order           = 'ASC' === strtoupper( $args['order'] ) ? 'ASC' : 'DESC';

		// Prepare query.
		$query = $wpdb->prepare(
			"SELECT * FROM {$this->table_name} WHERE {$where_clause} ORDER BY {$orderby} {$order} LIMIT %d OFFSET %d",
			absint( $args['limit'] ),
			absint( $args['offset'] )
		);

		// Get results.
		$results = $wpdb->get_results( $query, ARRAY_A );

		// Decode details JSON.
		if ( ! empty( $results ) ) {
			foreach ( $results as &$result ) {
				if ( ! empty( $result['details'] ) ) {
					$result['details'] = json_decode( $result['details'], true );
				}
			}
		}

		return $results;
	}

	/**
	 * Clear old logs.
	 *
	 * @param int $days Number of days to keep logs. Default 30.
	 * @return int Number of rows deleted.
	 */
	public function clear_old_logs( $days = 30 ) {
		global $wpdb;

		$days = absint( $days );

		$result = $wpdb->query(
			$wpdb->prepare(
				"DELETE FROM {$this->table_name} WHERE timestamp < DATE_SUB(NOW(), INTERVAL %d DAY)",
				$days
			)
		);

		return $result;
	}
}
