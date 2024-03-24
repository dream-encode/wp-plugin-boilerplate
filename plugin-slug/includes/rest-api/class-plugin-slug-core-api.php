<?php
/**
 * Class PLUGIN_CLASS_PREFIX_Core_API
 *
 * @since 1.0.0
 */

namespace PLUGIN_NAMESPACE\Core\RestApi;

use PLUGIN_NAMESPACE\Core\Abstracts\PLUGIN_CLASS_PREFIX_Abstract_API;

defined( 'ABSPATH' ) || exit;

/**
 * Class PLUGIN_CLASS_PREFIX_Core_API
 *
 * @since 1.0.0
 */
class PLUGIN_CLASS_PREFIX_Core_API extends PLUGIN_CLASS_PREFIX_Abstract_API {
	/**
	 * Includes files
	 *
	 * @since  1.0.0
	 * @return void
	 */
	public function rest_api_includes() {
		parent::rest_api_includes();

		$path_version = 'includes/rest-api' . DIRECTORY_SEPARATOR . $this->version . DIRECTORY_SEPARATOR . 'frontend';

		include_once PLUGIN_DEFINE_PREFIX_PLUGIN_PATH . $path_version . '/class-PLUGIN_SLUG-rest-user-controller.php';
	}

	/**
	 * Register all routes.
	 *
	 * @since  1.0.0
	 * @return void
	 */
	public function rest_api_register_routes() {
		$controllers = array(
			'PLUGIN_CLASS_PREFIX_REST_User_Controller',
		);

		$this->controllers = $controllers;

		parent::rest_api_register_routes();
	}
}
