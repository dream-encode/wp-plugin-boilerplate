<?php
/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://dream-encode.com
 * @since             1.0.0
 * @package           PLUGIN_CLASS_PREFIX
 *
 * @wordpress-plugin
 * Plugin Name:       PLUGIN_NAME
 * Plugin URI:        https://maxmarineelectronics.com
 * Description:       PLUGIN_DESCRIPTION
 * Version:           1.0.0
 * Author:            David Baumwald
 * Author URI:        https://dream-encode.com
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       PLUGIN_SLUG
 * Domain Path:       /languages
 * GitHub Plugin URI: GH_REPO_URL
 * Primary Branch:    main
 * Release Asset:     true
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Constants
 */
require_once 'includes/PLUGIN_SLUG-constants.php';

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-PLUGIN_SLUG-activator.php
 *
 * @return void
 */
function PLUGIN_FUNC_PREFIX_activate() {
	require_once PLUGIN_DEFINE_PREFIX_PLUGIN_PATH . 'includes/class-PLUGIN_SLUG-activator.php';
	PLUGIN_NAMESPACE\Core\PLUGIN_CLASS_PREFIX_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-PLUGIN_SLUG-deactivator.php
 *
 * @return void
 */
function PLUGIN_FUNC_PREFIX_deactivate() {
	require_once PLUGIN_DEFINE_PREFIX_PLUGIN_PATH . 'includes/class-PLUGIN_SLUG-deactivator.php';
	PLUGIN_NAMESPACE\Core\PLUGIN_CLASS_PREFIX_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'PLUGIN_FUNC_PREFIX_activate' );
register_deactivation_hook( __FILE__, 'PLUGIN_FUNC_PREFIX_deactivate' );

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since  1.0.0
 * @return void
 */
function PLUGIN_FUNC_PREFIX_init() {
	/**
	 * Import some common functions.
	 */
	require_once PLUGIN_DEFINE_PREFIX_PLUGIN_PATH . 'includes/PLUGIN_SLUG-core-functions.php';

	/**
	 * Main plugin loader class.
	 */
	require_once PLUGIN_DEFINE_PREFIX_PLUGIN_PATH . 'includes/class-PLUGIN_SLUG.php';

	$plugin = new PLUGIN_NAMESPACE\Core\PLUGIN_CLASS_PREFIX();
	$plugin->run();
}

PLUGIN_FUNC_PREFIX_init();
