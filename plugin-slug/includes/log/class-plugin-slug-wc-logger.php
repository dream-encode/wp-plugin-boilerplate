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

/**
 * Simple logger class to log data to custom files.
 *
 * Relies on the bundled logger class in WooCommerce.
 *
 * @package  PLUGIN_NAMESPACE\Core\Log\PLUGIN_CLASS_PREFIX_WC_Logger
 * @author   David Baumwald <david@dream-encode.com>
 */
class PLUGIN_CLASS_PREFIX_WC_Logger {

	/**
	 * Log namespace.
	 *
	 * @since   1.0.0
	 * @access  protected
	 * @var     string  $namespace  Log namespace.
	 */
	protected static $namespace = 'PLUGIN_ABBR';

	/**
	 * Log data.
	 *
	 * @since  1.0.0
	 * @param  mixed  $data  Data to log.
	 * @return void
	 */
	public static function log( $data ) {
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

		switch ( wp_get_environment_type() ) {
			case 'production':
			case 'staging':
				$logger = \wc_get_logger();

				$logger->info( $data, array( 'source' => self::$namespace ) );
				break;

			case 'development':
			case 'local':
			default:
				error_log(
					sprintf(
						'%1$s: %2$s',
						'PLUGIN_SHORT_DEFINE_PREFIX',
						$data
					)
				);
				break;
		}
	}
}
