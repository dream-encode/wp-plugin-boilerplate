<?php
/**
 * Fired during plugin activation.
 *
 * @link       https://dream-encode.com
 * @since      1.0.0
 *
 * @package    PLUGIN_NAMESPACE
 * @subpackage PLUGIN_NAMESPACE/includes
 */

namespace PLUGIN_NAMESPACE\Core;

use PLUGIN_NAMESPACE\Core\Install\PLUGIN_CLASS_PREFIX_Install;

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    PLUGIN_NAMESPACE
 * @subpackage PLUGIN_NAMESPACE/includes
 * @author     David Baumwald <david@dream-encode.com>
 */
class PLUGIN_CLASS_PREFIX_Activator {
	/**
	 * Activator.
	 *
	 * Runs on plugin activation.
	 *
	 * @since  1.0.0
	 * @return void
	 */
	public static function activate() {
		PLUGIN_CLASS_PREFIX_Install::install();
	}
}
