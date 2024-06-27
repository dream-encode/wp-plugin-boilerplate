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
