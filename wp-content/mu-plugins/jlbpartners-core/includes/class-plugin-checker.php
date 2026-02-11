<?php
/**
 * Plugin Checker Class
 *
 * Handles checking for required plugins and displaying admin notices.
 *
 * @package JLBPartners_Core
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * JLB Partners Core Plugin Checker class.
 */
class JLBPartners_Core_Plugin_Checker {

	/**
	 * Required plugins.
	 *
	 * @var array
	 */
	private $required_plugins;

	/**
	 * Constructor.
	 */
	public function __construct() {
		$this->set_required_plugins();
		add_action( 'admin_notices', array( $this, 'display_plugin_notices' ) );
		add_action( 'admin_init', array( $this, 'check_plugins' ) );
	}

	/**
	 * Set required plugins.
	 */
	private function set_required_plugins() {
		$this->required_plugins = array(
			'advanced-custom-fields-pro/acf.php' => array(
				'name'        => 'Advanced Custom Fields Pro',
				'slug'        => 'advanced-custom-fields-pro',
				'is_premium'  => true,
				'required'    => true,
				'description' => 'Required for flexible content components.',
			),
			'wordpress-seo/wp-seo.php'           => array(
				'name'        => 'Yoast SEO',
				'slug'        => 'wordpress-seo',
				'is_premium'  => false,
				'required'    => false,
				'description' => 'Recommended for SEO optimization.',
			),
		);
	}

	/**
	 * Get required plugins.
	 *
	 * @return array Required plugins.
	 */
	public function get_required_plugins() {
		return $this->required_plugins;
	}

	/**
	 * Check if a plugin is active.
	 *
	 * @param string $plugin_path Plugin path (e.g., 'plugin-folder/plugin-file.php').
	 * @return bool True if active, false otherwise.
	 */
	private function is_plugin_active( $plugin_path ) {
		if ( ! function_exists( 'is_plugin_active' ) ) {
			require_once ABSPATH . 'wp-admin/includes/plugin.php';
		}
		return is_plugin_active( $plugin_path );
	}

	/**
	 * Check if a plugin is installed.
	 *
	 * @param string $plugin_path Plugin path (e.g., 'plugin-folder/plugin-file.php').
	 * @return bool True if installed, false otherwise.
	 */
	private function is_plugin_installed( $plugin_path ) {
		$plugin_file = WP_PLUGIN_DIR . '/' . $plugin_path;
		return file_exists( $plugin_file );
	}

	/**
	 * Get missing plugins.
	 *
	 * @return array Array of missing plugins.
	 */
	public function get_missing_plugins() {
		$missing = array();

		foreach ( $this->required_plugins as $plugin_path => $plugin_info ) {
			if ( ! $this->is_plugin_active( $plugin_path ) && $plugin_info['required'] ) {
				$plugin_info['path']      = $plugin_path;
				$plugin_info['installed'] = $this->is_plugin_installed( $plugin_path );
				$missing[]                = $plugin_info;
			}
		}

		return $missing;
	}

	/**
	 * Check plugins and log any issues.
	 */
	public function check_plugins() {
		// Only check if JLB Partners theme is active.
		$current_theme = wp_get_theme();
		if ( 'JLB Partners' !== $current_theme->get( 'Name' ) && 'jlbpartners' !== $current_theme->get_template() ) {
			return;
		}

		$missing = $this->get_missing_plugins();

		if ( ! empty( $missing ) ) {
			$logger = jlbpartners_core()->logger;
			$logger->log(
				sprintf( '%d required plugins are missing or inactive', count( $missing ) ),
				'warning',
				'plugin',
				array( 'missing_plugins' => $missing )
			);
		}
	}

	/**
	 * Display plugin notices in admin.
	 */
	public function display_plugin_notices() {
		// Only show if JLB Partners theme is active.
		$current_theme = wp_get_theme();
		if ( 'JLB Partners' !== $current_theme->get( 'Name' ) && 'jlbpartners' !== $current_theme->get_template() ) {
			return;
		}

		$missing = $this->get_missing_plugins();

		if ( empty( $missing ) ) {
			return;
		}

		// Show notice for missing plugins.
		?>
		<div class="notice notice-error is-dismissible">
			<h3><?php esc_html_e( 'JLB Partners Theme: Required Plugins Missing', 'jlbpartners-core' ); ?></h3>
			<p><?php esc_html_e( 'The JLB Partners theme requires the following plugins to function properly:', 'jlbpartners-core' ); ?></p>
			<ul style="list-style: disc; margin-left: 20px;">
				<?php foreach ( $missing as $plugin ) : ?>
					<li>
						<strong><?php echo esc_html( $plugin['name'] ); ?></strong>
						<?php if ( $plugin['installed'] ) : ?>
							- <span style="color: #d63638;"><?php esc_html_e( 'Installed but not activated', 'jlbpartners-core' ); ?></span>
							<?php if ( current_user_can( 'activate_plugins' ) ) : ?>
								- <a href="<?php echo esc_url( admin_url( 'plugins.php' ) ); ?>"><?php esc_html_e( 'Activate Now', 'jlbpartners-core' ); ?></a>
							<?php endif; ?>
						<?php else : ?>
							- <span style="color: #d63638;"><?php esc_html_e( 'Not installed', 'jlbpartners-core' ); ?></span>
							<?php if ( $plugin['is_premium'] ) : ?>
								- <?php esc_html_e( 'This is a premium plugin. Please install manually.', 'jlbpartners-core' ); ?>
							<?php else : ?>
								<?php if ( current_user_can( 'install_plugins' ) ) : ?>
									- <a href="<?php echo esc_url( admin_url( 'plugin-install.php?s=' . rawurlencode( $plugin['slug'] ) . '&tab=search&type=term' ) ); ?>"><?php esc_html_e( 'Install Now', 'jlbpartners-core' ); ?></a>
								<?php endif; ?>
							<?php endif; ?>
						<?php endif; ?>
						<br><em><?php echo esc_html( $plugin['description'] ); ?></em>
					</li>
				<?php endforeach; ?>
			</ul>
		</div>
		<?php
	}

	/**
	 * Check if all required plugins are active.
	 *
	 * @return bool True if all required plugins are active, false otherwise.
	 */
	public function are_all_plugins_active() {
		return empty( $this->get_missing_plugins() );
	}
}
