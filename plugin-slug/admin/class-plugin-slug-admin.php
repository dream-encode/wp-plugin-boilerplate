<?php
/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://dream-encode.com
 * @since      1.0.0
 *
 * @package    PLUGIN_CLASS_PREFIX
 * @subpackage PLUGIN_CLASS_PREFIX/admin
 */

namespace PLUGIN_NAMESPACE\Admin;

use WP_Screen;

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    PLUGIN_CLASS_PREFIX
 * @subpackage PLUGIN_CLASS_PREFIX/admin
 * @author     David Baumwald <david@dream-encode.com>
 */
class PLUGIN_CLASS_PREFIX_Admin {

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since  1.0.0
	 * @return void
	 */
	public function enqueue_styles() {
		$screen = get_current_screen();

		if ( ! $screen instanceof WP_Screen ) {
			return;
		}

		$assets_version = ( 'development' === wp_get_environment_type() ) ? (string) time() : PLUGIN_DEFINE_PREFIX_CSS_VERSION;

		$asset_base_path = plugins_url( '/', __FILE__ );
	}
}
