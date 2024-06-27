<?php
/**
 * Abstract logger class that relies on the WC_Logger instance.
 *
 * @link       https://dream-encode.com
 * @since      1.0.0
 *
 * @package    PLUGIN_CLASS_PREFIX/includes/abstracts
 */

namespace PLUGIN_NAMESPACE\Core\Abstracts;

/**
 * Abstract logger class to log data to custom files.
 *
 * Relies on the bundled logger class in WooCommerce.
 *
 * @package  PLUGIN_NAMESPACE\Core\Abstracts\PLUGIN_CLASS_PREFIX_Abstract_WC_Logger
 * @author   David Baumwald <david@dream-encode.com>
 */
class PLUGIN_CLASS_PREFIX_Abstract_WC_Logger {

	/**
	 * Log namespace.
	 *
	 * @since  1.0.0
	 * @access protected
	 * @var    string  $namespace  Log namespace.
	 */
	protected static $namespace = 'PLUGIN_SLUG';

	/**
	 * Log levels.
	 *
	 * @since  1.0.0
	 * @access protected
	 * @var    string[]  $log_levels  Log levels.
	 */
	protected static $log_levels = array(
		'emergency',
		'alert',
		'critical',
		'error',
		'warning',
		'notice',
		'info',
		'debug',
	);

	/**
	 * Log data.
	 *
	 * @since  1.0.0
	 * @param  mixed   $data   Data to log.
	 * @param  string  $level  Optional. Data to log. Default 'debug'.
	 * @return void
	 */
	public static function log( $data, $level = 'debug' ) {
		if ( ! in_array( $level, static::$log_levels, true ) ) {
			return;
		}

		// Next, check the plugin setting for the desired log level.
		$log_level_setting = PLUGIN_FUNC_PREFIX_get_plugin_setting( 'log-level', 'off' );

		if ( 'off' === $log_level_setting || ! in_array( $log_level_setting, static::$log_levels, true ) ) {
			return;
		}

		static $loggable_log_levels = false;

		if ( false === $loggable_log_levels ) {
			$level_index = array_search( $log_level_setting, static::$log_levels );

			if ( false === $level_index ) {
				return;
			}

			$loggable_log_levels = array_slice( static::$log_levels, 0, intval( $level_index ) + 1 );
		}

		// Check if the level is a loggable level.
		if ( ! is_array( $loggable_log_levels ) || ! in_array( $level, $loggable_log_levels, true ) ) {
			return;
		}

		if ( ! function_exists( 'wc_get_logger' ) ) {
			// @phpcs:ignore
			error_log(
				sprintf(
					/* translators: 1: WC_Logger class name, 2: `log` method name.`. */
					__( '%1$s->%2$s depends on `wc_get_logger` which is bundled with the WooCommerce plugin.', 'PLUGIN_SLUG' ),
					__CLASS__,
					__METHOD__
				)
			);
			return;
		}

		if ( is_object( $data ) || is_array( $data ) ) {
			$data = print_r( $data, true );
		}

		$logger = \wc_get_logger();

		$logger->{$level}( $data, array( 'source' => self::$namespace ) );
	}

	/**
	 * Adds an emergency level message.
	 *
	 * @since  1.0.0
	 * @param  mixed  $data  Data to log.
	 * @return void
	 */
	public static function emergency( $data ) {
		static::log( $data, 'emergency' );
	}

	/**
	 * Adds an alert level message.
	 *
	 * @since  1.0.0
	 * @param  mixed  $data  Data to log.
	 * @return void
	 */
	public static function alert( $data ) {
		static::log( $data, 'alert' );
	}

	/**
	 * Adds an critical level message.
	 *
	 * @since  1.0.0
	 * @param  mixed  $data  Data to log.
	 * @return void
	 */
	public static function critical( $data ) {
		static::log( $data, 'critical' );
	}

	/**
	 * Adds an error level message.
	 *
	 * @since  1.0.0
	 * @param  mixed  $data  Data to log.
	 * @return void
	 */
	public static function error( $data ) {
		static::log( $data, 'error' );
	}

	/**
	 * Adds an warning level message.
	 *
	 * @since  1.0.0
	 * @param  mixed  $data  Data to log.
	 * @return void
	 */
	public static function warning( $data ) {
		static::log( $data, 'warning' );
	}

	/**
	 * Adds an notice level message.
	 *
	 * @since  1.0.0
	 * @param  mixed  $data  Data to log.
	 * @return void
	 */
	public static function notice( $data ) {
		static::log( $data, 'notice' );
	}

	/**
	 * Adds an info level message.
	 *
	 * @since  1.0.0
	 * @param  mixed  $data  Data to log.
	 * @return void
	 */
	public static function info( $data ) {
		static::log( $data, 'info' );
	}

	/**
	 * Adds an debug level message.
	 *
	 * @since  1.0.0
	 * @param  mixed  $data  Data to log.
	 * @return void
	 */
	public static function debug( $data ) {
		static::log( $data, 'debug' );
	}
}
