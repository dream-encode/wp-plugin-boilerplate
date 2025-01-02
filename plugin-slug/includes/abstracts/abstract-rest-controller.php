<?php
/**
 * Class PLUGIN_CLASS_PREFIX_Abstract_REST_Controller
 */

namespace PLUGIN_NAMESPACE\Core\Abstracts;

use WP_REST_Controller;
use PLUGIN_NAMESPACE\Core\RestApi\PLUGIN_CLASS_PREFIX_REST_Authentication;

/**
 * Class PLUGIN_CLASS_PREFIX_Abstract_REST_Controller
 */
class PLUGIN_CLASS_PREFIX_Abstract_REST_Controller extends WP_REST_Controller {
	/**
	 * The current namespace.
	 *
	 * @since   1.0.0
	 * @access  public
	 * @var     string   $namespace  The current namespace.
	 */
	public $namespace = 'PLUGIN_HOOK_PREFIX/v1';

	/**
	 * The current rest_base.
	 *
	 * @since   1.0.0
	 * @access  public
	 * @var     string   $rest_base  The current rest_base.
	 */
	public $rest_base = '';

	/**
	 * Array of routes.
	 *
	 * @since   1.0.0
	 * @access  public
	 * @var     array   $routes  Array of routes.
	 */
	public $routes = array();

	/**
	 * Register routes for controller.
	 *
	 * @since  1.0.0
	 * @return void
	 */
	public function register_routes() {
		if ( ! $this->routes ) {
			return;
		}

		foreach ( $this->routes as $key => $args ) {
			$rest_base = $this->rest_base;
			$override  = false;

			if ( is_bool( end( $args ) ) ) {
				$override = array_pop( $args );
			}

			if ( ! is_numeric( $key ) ) {
				$rest_base = "{$rest_base}/{$key}";
			}

			register_rest_route( $this->namespace, '/' . $rest_base, $args, $override );
		}
	}

	/**
	 * Ensure rest response.
	 *
	 * @since  1.0.0
	 * @param  mixed  $data  Current response data.
	 * @return mixed
	 */
	public function ensure_response( $data ) {
		return rest_ensure_response( $data );
	}

	/**
	 * Check user is Admin
	 *
	 * @since  1.0.0
	 * @return bool
	 */
	public function check_admin_permission() {
		return PLUGIN_CLASS_PREFIX_REST_Authentication::check_admin_permission();
	}

	/**
	 * Check user can do action
	 *
	 * @since  1.0.0
	 * @return bool
	 */
	public function check_user_permission() {
		return PLUGIN_CLASS_PREFIX_REST_Authentication::check_user_permission();
	}
}
