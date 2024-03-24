<?php
/**
 * Define the internationalization functionality
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @link       https://dream-encode.com
 * @since      1.0.0
 *
 * @package    PLUGIN_CLASS_PREFIX
 * @subpackage PLUGIN_CLASS_PREFIX/includes
 */

namespace PLUGIN_NAMESPACE\Core;

/**
 * Define the internationalization functionality.
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @since      1.0.0
 * @package    PLUGIN_CLASS_PREFIX
 * @subpackage PLUGIN_CLASS_PREFIX/includes
 * @author     David Baumwald <david@dream-encode.com>
 */
class PLUGIN_CLASS_PREFIX_I18n {
	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since  1.0.0
	 * @return void
	 */
	public function load_plugin_textdomain() {
		load_plugin_textdomain(
			'PLUGIN_SLUG',
			false,
			PLUGIN_DEFINE_PREFIX_PLUGIN_PATH . 'languages/'
		);
	}
}
