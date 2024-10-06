<?php
/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://dream-encode.com
 * @since      1.0.0
 *
 * @package    PLUGIN_CLASS_PREFIX
 * @subpackage PLUGIN_CLASS_PREFIX/public
 */

namespace PLUGIN_NAMESPACE\Frontend;

use PLUGIN_NAMESPACE\Core\Upgrade\PLUGIN_CLASS_PREFIX_Upgrader;
use PLUGIN_NAMESPACE\Core\RestApi\PLUGIN_CLASS_PREFIX_Core_API;

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    PLUGIN_CLASS_PREFIX
 * @subpackage PLUGIN_CLASS_PREFIX/public
 * @author     David Baumwald <david@dream-encode.com>
 */
class PLUGIN_CLASS_PREFIX_Public {

	/**
	 * Do stuff when plugin updates happen.
	 *
	 * @since  1.0.0
	 * @param  object  $upgrader_object  Upgrader object.
	 * @param  array   $options          Options.
	 * @return void
	 */
	public function upgrader_process_complete( $upgrader_object, $options ) {
		if ( isset( $options['plugins'] ) && is_array( $options['plugins'] ) ) {
			foreach ( $options['plugins'] as $index => $plugin ) {
				if ( 'PLUGIN_SLUG/PLUGIN_SLUG.php' === $plugin ) {
					as_enqueue_async_action( 'PLUGIN_FUNC_PREFIX_process_plugin_upgrade', array(), 'PLUGIN_SLUG' );
					return;
				}
			}
		}
	}

	/**
	 * Maybe perform database migrations when a plugin upgrade occurs.
	 *
	 * @since  1.0.0
	 * @return void
	 */
	public function process_plugin_upgrade() {
		$upgrader = new PLUGIN_CLASS_PREFIX_Upgrader();

		$upgrader->maybe_upgrade();
	}

	/**
	 * Send the CORS header on REST requests.
	 *
	 * @since  1.0.0
	 * @return void
	 */
	public function rest_api_cors() {
		if ( 'production' === wp_get_environment_type() ) {
			return;
		}

		header( 'Access-Control-Allow-Origin: *' );
	}

	/**
	 * Initialize rest api instances.
	 *
	 * @since  1.0.0
	 * @return void
	 */
	public function rest_init() {
		$api = new PLUGIN_CLASS_PREFIX_Core_API();
	}

	/**
	 * Example function.
	 *
	 * @since  1.0.0
	 * @access public
	 * @param  string  $param  First function parameter.
	 * @return string
	 */
	public function example_function( $param ) {
		return $param;
	}
}
