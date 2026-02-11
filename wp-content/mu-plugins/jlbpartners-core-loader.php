<?php
/**
 * Plugin Name: JLB Partners Core Loader
 * Description: Loads the JLB Partners Core MU plugin.
 * Version: 1.0.0
 * Author: JLB Partners
 *
 * @package JLBPartners_Core
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Load the main plugin file.
require_once WPMU_PLUGIN_DIR . '/jlbpartners-core/jlbpartners-core.php';
