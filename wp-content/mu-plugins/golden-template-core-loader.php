<?php
/**
 * Plugin Name: Golden Template Core Loader
 * Description: Loads the Golden Template Core MU plugin.
 * Version: 1.0.0
 * Author: Golden Template
 *
 * @package GoldenTemplate_Core
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Load the main plugin file.
require_once WPMU_PLUGIN_DIR . '/golden-template-core/golden-template-core.php';
