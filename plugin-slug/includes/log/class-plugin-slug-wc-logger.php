<?php
/**
 * Simple logger class that relies on the WC_Logger instance.
 *
 * @link       https://dream-encode.com
 * @since      1.0.0
 *
 * @package    PLUGIN_CLASS_PREFIX/includes/log
 */

namespace PLUGIN_NAMESPACE\Core\Log;

use PLUGIN_NAMESPACE\Core\Abstracts\PLUGIN_CLASS_PREFIX_Abstract_WC_Logger;

/**
 * Simple logger class to log data to custom files.
 *
 * Relies on the bundled logger class in WooCommerce.
 *
 * @package  PLUGIN_NAMESPACE\Core\Log\PLUGIN_CLASS_PREFIX_WC_Logger
 * @author   David Baumwald <david@dream-encode.com>
 */
final class PLUGIN_CLASS_PREFIX_WC_Logger extends PLUGIN_CLASS_PREFIX_Abstract_WC_Logger {

	/**
	 * Log namespace.
	 *
	 * @since   1.0.0
	 * @access  protected
	 * @var     string  $namespace  Log namespace.
	 */
	protected static $namespace = 'PLUGIN_SLUG';
}
