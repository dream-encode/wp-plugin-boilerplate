<?php
/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       https://dream-encode.com
 * @since      1.0.0
 *
 * @package    PLUGIN_CLASS_PREFIX
 * @subpackage PLUGIN_CLASS_PREFIX/includes
 */

namespace PLUGIN_NAMESPACE\Core;

use PLUGIN_NAMESPACE\Core\PLUGIN_CLASS_PREFIX_Loader;
use PLUGIN_NAMESPACE\Core\PLUGIN_CLASS_PREFIX_I18n;
use PLUGIN_NAMESPACE\Admin\PLUGIN_CLASS_PREFIX_Admin;
use PLUGIN_NAMESPACE\Frontend\PLUGIN_CLASS_PREFIX_Public;
use PLUGIN_NAMESPACE\Core\Upgrade\PLUGIN_CLASS_PREFIX_Upgrader;

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    PLUGIN_CLASS_PREFIX
 * @subpackage PLUGIN_CLASS_PREFIX/includes
 * @author     David Baumwald <david@dream-encode.com>
 */
class PLUGIN_CLASS_PREFIX {
	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since   1.0.0
	 * @access  protected
	 * @var     PLUGIN_CLASS_PREFIX_Loader    $loader    Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since   1.0.0
	 * @access  protected
	 * @var     string    $plugin_name    The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @since   1.0.0
	 * @access  protected
	 * @var     string    $version    The current version of the plugin.
	 */
	protected $version;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		$this->plugin_name = 'PLUGIN_SLUG';

		$this->load_dependencies();
		$this->define_tables();
		$this->set_locale();
		$this->define_admin_hooks();
		$this->define_public_hooks();
	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - PLUGIN_CLASS_PREFIX_Loader. Orchestrates the hooks of the plugin.
	 * - PLUGIN_CLASS_PREFIX_I18n. Defines internationalization functionality.
	 * - PLUGIN_CLASS_PREFIX_Admin. Defines all hooks for the admin area.
	 * - PLUGIN_CLASS_PREFIX_Public. Defines all hooks for the public side of the site.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since  1.0.0
	 * @access private
	 * @return void
	 */
	private function load_dependencies() {

		/**
		 * Logger
		 */
		require_once PLUGIN_DEFINE_PREFIX_PLUGIN_PATH . 'includes/abstracts/abstract-wc-logger.php';
		require_once PLUGIN_DEFINE_PREFIX_PLUGIN_PATH . 'includes/log/class-PLUGIN_SLUG-wc-logger.php';

		/**
		 * Upgrader.
		 */
		require_once PLUGIN_DEFINE_PREFIX_PLUGIN_PATH . 'includes/upgrade/class-PLUGIN_SLUG-upgrader.php';

		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
		require_once PLUGIN_DEFINE_PREFIX_PLUGIN_PATH . 'includes/class-PLUGIN_SLUG-loader.php';

		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		require_once PLUGIN_DEFINE_PREFIX_PLUGIN_PATH . 'includes/class-PLUGIN_SLUG-i18n.php';

		/**
		 * Default filters.
		 */
		require_once PLUGIN_DEFINE_PREFIX_PLUGIN_PATH . 'includes/PLUGIN_SLUG-default-filters.php';

		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once PLUGIN_DEFINE_PREFIX_PLUGIN_PATH . 'admin/class-PLUGIN_SLUG-admin.php';

		/**
		 * The class responsible for defining all actions that occur in the public-facing
		 * side of the site.
		 */
		require_once PLUGIN_DEFINE_PREFIX_PLUGIN_PATH . 'public/class-PLUGIN_SLUG-public.php';

		PLUGIN_CLASS_PREFIX_Upgrader::init();

		$this->loader = new PLUGIN_CLASS_PREFIX_Loader();
	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the PLUGIN_CLASS_PREFIX_I18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since  1.0.0
	 * @access private
	 * @return void
	 */
	private function set_locale() {
		$plugin_i18n = new PLUGIN_CLASS_PREFIX_I18n();

		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );
	}

	/**
	 * Define custom databases tables.
	 *
	 * @since  1.0.0
	 * @return void
	 */
	public function define_tables() {
		PLUGIN_CLASS_PREFIX_Upgrader::define_tables();
	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since  1.0.0
	 * @access private
	 * @return void
	 */
	private function define_admin_hooks() {
		$plugin_admin = new PLUGIN_CLASS_PREFIX_Admin();

		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since  1.0.0
	 * @access private
	 * @return void
	 */
	private function define_public_hooks() {
		$plugin_public = new PLUGIN_CLASS_PREFIX_Public();

		$this->loader->add_action( 'example_function', $plugin_public, 'example_function' );
	}

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since  1.0.0
	 * @return void
	 */
	public function run() {
		$this->loader->run();
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since  1.0.0
	 * @return string  The name of the plugin.
	 */
	public function get_plugin_name() {
		return $this->plugin_name;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since  1.0.0
	 * @return PLUGIN_CLASS_PREFIX_Loader  Orchestrates the hooks of the plugin.
	 */
	public function get_loader() {
		return $this->loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since  1.0.0
	 * @return string  The version number of the plugin.
	 */
	public function get_version() {
		return $this->version;
	}
}
