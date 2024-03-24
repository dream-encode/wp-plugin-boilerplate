<?php
/**
 * Class PLUGIN_CLASS_PREFIX_CLI_Commands_Scripts
 *
 * Base class for custom WP_CLI commands.
 *
 * @since 1.0.0
 */

namespace PLUGIN_NAMESPACE\Core\CLI;

use WP_CLI;
use WP_Term;
use WC_Product;
use WC_Product_Attribute;
use PLUGIN_NAMESPACE\Core\Log\PLUGIN_CLASS_PREFIX_WC_Logger;

// @phpcs:disable
/**
 * Class PLUGIN_CLASS_PREFIX_CLI_Commands_Scripts
 *
 * Base class for custom WP_CLI commands for Scripts.
 *
 * @since 1.0.0
 */
class PLUGIN_CLASS_PREFIX_CLI_Commands_Scripts {
	/**
	 * A simple example command.
	 *
	 * @since  1.0.0
	 * @access public
	 * @global WPDB   $wpdb        The WordPress database abstraction object.
	 * @param  array  $args        Indexed array of arguments.
	 * @param  array  $assoc_args  Assoc array of arguments.
	 * @return void
	 */
	public function example_command( $args, $assoc_args ) {
		global $wpdb;

		$dry_run = ! empty( $assoc_args['dry-run'] );

		$limit = ( isset( $assoc_args['limit'] ) ) ? intval( $assoc_args['limit'] ) : 10;

		if ( isset( $assoc_args['all'] ) ) {
			$limit = -1;
		}

		$total_count = 0;
		$fixed_count = 0;

		return;
	}

	/**
	 * Log messages to log files and the CLI output.
	 *
	 * @since  1.0.0
	 * @access public
	 * @param  mixed    $message  Message to log.
	 * @param  boolean  $stdout   Optional. Whether to output the message to the CLI stdout. Default true.
	 * @return void
	 */
	public function log( $message, $stdout = true ) {
		PLUGIN_CLASS_PREFIX_WC_Logger::log( $message );

		if ( function_exists( 'ray' ) ) {
			ray( $message ); // @phpstan-ignore-line
		}

		if ( $stdout ) {
			WP_CLI::line( $message );
		}
	}
}
