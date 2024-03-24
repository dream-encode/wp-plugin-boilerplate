<?php
/**
/**
 * Class PLUGIN_CLASS_PREFIX
 *
 * @since 1.0.0
 */

namespace PLUGIN_NAMESPACE\Core\Install;

use PLUGIN_NAMESPACE\Core\PLUGIN_CLASS_PREFIX_Functions;

defined( 'ABSPATH' ) || exit;

/**
 * Class PLUGIN_CLASS_PREFIX
 *
 * @since 1.0.0
 */
class PLUGIN_CLASS_PREFIX {

	/**
	 * DB updates and callbacks that need to be run per version.
	 *
	 * Please note that these functions are invoked when the plugin is updated from a previous version,
	 * but NOT when the plugin is newly installed.
	 *
	 * Database schema changes must be incorporated to the SQL returned by get_schema, which is applied
	 * via dbDelta at both install and update time.
	 *
	 * @var array<mixed>
	 */
	private static $db_updates = array();

	/**
	 * Hook in tabs.
	 *
	 * @since  1.0.0
	 * @return void
	 */
	public static function init() {
		add_action( 'init', array( __CLASS__, 'check_version' ), 5 );
		add_action( 'PLUGIN_ABBR_run_update_callback', array( __CLASS__, 'run_update_callback' ) );
		add_action( 'PLUGIN_ABBR_update_db_to_current_version', array( __CLASS__, 'update_db_version' ) );
	}

	/**
	 * Check plugin version and run the updater is required.
	 *
	 * This check is done on all requests and runs if the versions do not match.
	 *
	 * @since  1.0.0
	 * @return void
	 */
	public static function check_version() {
		$plugin_version      = get_option( 'PLUGIN_ABBR_plugin_version' );
		$plugin_code_version = PLUGIN_DEFINE_PREFIX_PLUGIN_VERSION;
		$requires_update     = version_compare( $plugin_version, $plugin_code_version, '<' );

		if ( $requires_update ) {
			self::install();
		}
	}

	/**
	 * Run an update callback when triggered by ActionScheduler.
	 *
	 * @since  1.0.0
	 * @param  string  $update_callback  Callback name.
	 * @return void
	 */
	public static function run_update_callback( $update_callback ) {
		include_once PLUGIN_DEFINE_PREFIX_PLUGIN_PATH . 'includes/PLUGIN_SLUG-update-functions.php';

		if ( is_callable( $update_callback ) ) {
			self::run_update_callback_start( $update_callback );

			$result = (bool) call_user_func( $update_callback );

			self::run_update_callback_end( $update_callback, $result );
		}
	}

	/**
	 * Triggered when a callback will run.
	 *
	 * @since  1.0.0
	 * @param  string  $callback  Callback name.
	 * @return void
	 */
	protected static function run_update_callback_start( $callback ) {
		PLUGIN_ABBR_maybe_define_constant( 'PLUGIN_SHORT_DEFINE_PREFIX_UPDATING', true );
	}

	/**
	 * Triggered when a callback has ran.
	 *
	 * @since  1.0.0
	 * @param  string  $callback  Callback name.
	 * @param  bool    $result    Return value from callback. Non-false need to run again.
	 * @return void
	 */
	protected static function run_update_callback_end( $callback, $result ) {
		if ( $result && is_callable( $callback ) ) {
			$callback( $result );
		}
	}

	/**
	 * Install plugin.
	 *
	 * @since  1.0.0
	 * @return void
	 */
	public static function install() {
		if ( ! is_blog_installed() ) {
			return;
		}

		// Check if we are not already running this routine.
		if ( self::is_installing() ) {
			return;
		}

		// If we made it here nothing is running yet, lets set the transient now.
		set_transient( 'PLUGIN_ABBR_installing', 'yes', MINUTE_IN_SECONDS * 10 );

		PLUGIN_ABBR_maybe_define_constant( 'PLUGIN_SHORT_DEFINE_PREFIX_INSTALLING', true );

		self::create_tables();

		self::create_options();

		self::update_plugin_version();

		self::maybe_update_db_version();

		delete_transient( 'PLUGIN_ABBR_installing' );
	}

	/**
	 * Returns true if we're installing.
	 *
	 * @since  1.0.0
	 * @return bool
	 */
	private static function is_installing() {
		return 'yes' === get_transient( 'PLUGIN_ABBR_installing' );
	}

	/**
	 * Is this a brand new plugin install?
	 *
	 * A brand new install has no version yet. Also treat empty installs as 'new'.
	 *
	 * @since  1.0.0
	 * @return boolean
	 */
	public static function is_new_install() {
		return is_null( get_option( 'PLUGIN_ABBR_plugin_version', null ) );
	}

	/**
	 * Is a DB update needed?
	 *
	 * @since  1.0.0
	 * @return boolean
	 */
	public static function needs_db_update() {
		$updates = self::get_db_update_callbacks();

		if ( count( $updates ) < 1 ) {
			return false;
		}

		$current_db_version = get_option( 'PLUGIN_ABBR_plugin_db_version', null );

		$update_versions    = array_keys( $updates );

		// @phpstan-ignore-next-line
		usort( $update_versions, 'version_compare' );

		return ! is_null( $current_db_version ) && version_compare( $current_db_version, end( $update_versions ), '<' );
	}

	/**
	 * See if we need to show or run database updates during install.
	 *
	 * @since  1.0.0
	 * @return void
	 */
	private static function maybe_update_db_version() {
		if ( self::needs_db_update() ) {
			self::update();
		} else {
			self::update_db_version();
		}
	}

	/**
	 * Update plugin version to current.
	 *
	 * @since  1.0.0
	 * @return void
	 */
	private static function update_plugin_version() {
		update_option( 'PLUGIN_ABBR_plugin_version', PLUGIN_DEFINE_PREFIX_PLUGIN_VERSION );
	}

	/**
	 * Get list of DB update callbacks.
	 *
	 * @since  1.0.0
	 * @return array<mixed>
	 */
	public static function get_db_update_callbacks() {
		return self::$db_updates;
	}

	/**
	 * Push all needed DB updates to the queue for processing.
	 *
	 * @since  1.0.0
	 * @return void
	 */
	private static function update() {
		$current_db_version = get_option( 'PLUGIN_ABBR_plugin_db_version' );

		foreach ( self::get_db_update_callbacks() as $version => $update_callbacks ) {
			if ( version_compare( $current_db_version, $version, '<' ) ) {
				foreach ( $update_callbacks as $update_callback ) {
					$update_callback();
				}
			}
		}

		// After the callbacks finish, update the db version to the current plugin version.
		$current_db_define_version = PLUGIN_DEFINE_PREFIX_PLUGIN_VERSION;

		if ( version_compare( $current_db_version, $current_db_define_version, '<' ) ) {
			self::update_db_version( $current_db_define_version );
		}
	}

	/**
	 * Update DB version to current.
	 *
	 * @since  1.0.0
	 * @param  string|null  $version  New plugin DB version or null.
	 * @return void
	 */
	public static function update_db_version( $version = null ) {
		update_option( 'PLUGIN_ABBR_plugin_db_version', is_null( $version ) ? PLUGIN_DEFINE_PREFIX_DATABASE_VERSION : $version );
	}

	/**
	 * Default options.
	 *
	 * Sets up the default options used on the settings page.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	private static function create_options() {
		if ( ! get_option( 'PLUGIN_FUNC_PREFIX_settings' ) ) {
			$defaults = array();

			add_option( 'PLUGIN_FUNC_PREFIX_settings', $defaults );
		}
	}
	/**

	/**
	 * Set up the database tables which the plugin needs to function.
	 * WARNING: If you are modifying this method, make sure that its safe to call regardless of the state of database.
	 *
	 * This is called from `install` method and is executed in-sync when the plugin is installed or updated.
	 *
	 * @since 1.0.0
	 * @global WPDB  $wpdb  WordPress database instance global.
	 * @return void
	 */
	public static function create_tables() {
		global $wpdb;

		$wpdb->hide_errors();

		require_once ABSPATH . 'wp-admin/includes/upgrade.php';

		dbDelta( self::get_schema() );
	}

	/**
	 * Get Table schema.
	 *
	 * Changing indexes may cause duplicate index notices in logs due to https://core.trac.wordpress.org/ticket/34870 but dropping
	 * indexes first causes too much load on some servers/larger DB.
	 *
	 * When adding or removing a table, make sure to update the list of tables in Max_Marine_Enhanced_product_Changelogs_Install::get_tables().
	 *
	 * @since  1.0.0
	 * @global WPDB  $wpdb  WordPress database instance global.
	 * @return string
	 */
	private static function get_schema() {
		global $wpdb;

		$collate = '';

		if ( $wpdb->has_cap( 'collation' ) ) {
			$collate = $wpdb->get_charset_collate();
		}

		$tables = "";

		return $tables;
	}

	/**
	 * Return a list of plugin tables. Used to make sure all tables are dropped when uninstalling the plugin
	 * in a single site or multi site environment.
	 *
	 * @since  1.0.0
	 * @global WPDB  $wpdb  WordPress database instance global.
	 * @return array<string, string>  Database tables.
	 */
	public static function get_tables() {
		global $wpdb;

		$tables = array();

		return $tables;
	}

	/**
	 * Drop plugin tables.
	 *
	 * @since  1.0.0
	 * @global WPDB  $wpdb  WordPress database instance global.
	 * @return void
	 */
	public static function drop_tables() {
		global $wpdb;

		$tables = static::get_tables();

		foreach ( $tables as $name => $table ) {
			$wpdb->query( "DROP TABLE IF EXISTS {$table}" ); // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
		}
	}

	/**
	 * Define plugin tables in the `$wpdb` global.
	 *
	 * @since  1.0.0
	 * @global WPDB  $wpdb  WordPress database instance global.
	 * @return void
	 */
	public static function define_tables() {
		global $wpdb;

		$tables = static::get_tables();

		foreach ( $tables as $name => $table ) {
			$wpdb->{$name} = $table;

			$wpdb->tables[] = $name;
		}
	}
}

PLUGIN_CLASS_PREFIX::init();
