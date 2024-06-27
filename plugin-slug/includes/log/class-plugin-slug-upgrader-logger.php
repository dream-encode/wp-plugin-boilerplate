<?php
/**
 * Simple wrapper class for custom logs.
 *
 * @uses \WC_Logger();
 *
 * @link       https://dream-encode.com
 * @since      1.0.0
 *
 * @package    PLUGIN_CLASS_PREFIX
 * @subpackage PLUGIN_CLASS_PREFIX/includes
 */

namespace PLUGIN_NAMESPACE\Core\Log;

use PLUGIN_NAMESPACE\Core\Abstracts\PLUGIN_CLASS_PREFIX_Abstract_WC_Logger;

/**
 * Logger class.
 *
 * Log stuff to files.
 *
 * @since      1.0.0
 * @package    PLUGIN_CLASS_PREFIX
 * @subpackage PLUGIN_CLASS_PREFIX/includes
 * @author     David Baumwald <david@dream-encode.com>
 */
final class PLUGIN_CLASS_PREFIX_Upgrader_Logger extends PLUGIN_CLASS_PREFIX_Abstract_WC_Logger {
	/**
	 * Log namespace.
	 *
	 * @since   1.0.0
	 * @access  protected
	 * @var     string  $namespace  Log namespace.
	 */
	public static $namespace = 'PLUGIN_SLUG-upgrader';
}
