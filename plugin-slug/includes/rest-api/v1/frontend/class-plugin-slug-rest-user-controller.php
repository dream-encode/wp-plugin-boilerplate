<?php
/**
 * Class PLUGIN_CLASS_PREFIX_REST_Example_Controller
 */

namespace PLUGIN_NAMESPACE\Core\RestApi\V1\Frontend;

use Exception;
use WP_REST_Server;
use WP_REST_Request;
use WP_REST_Response;
use WP_Error;
use PLUGIN_NAMESPACE\Core\RestApi\PLUGIN_CLASS_PREFIX_REST_Response;
use PLUGIN_NAMESPACE\Core\Abstracts\PLUGIN_CLASS_PREFIX_Abstract_REST_Controller;


/**
 * Class PLUGIN_CLASS_PREFIX_REST_Example_Controller
 */
class PLUGIN_CLASS_PREFIX_REST_Example_Controller extends PLUGIN_CLASS_PREFIX_Abstract_REST_Controller {
	/**
	 * PLUGIN_CLASS_PREFIX_REST_Example_Controller constructor.
	 */
	public function __construct() {
		$this->namespace = 'PLUGIN_HOOK_PREFIX/v1';
		$this->rest_base = 'example';
	}

	/**
	 * Register routes API
	 *
	 * @since  1.0.0
	 * @return void
	 */
	public function register_routes() {
		$this->routes = array(
			'example' => array(
				array(
					'methods'             => WP_REST_Server::CREATABLE,
					'callback'            => array( $this, 'example_method' ),
					'permission_callback' => array( $this, 'permission_callback' ),
				),
			),
		);

		parent::register_routes();
	}

	/**
	 * Validate user permissions.
	 *
	 * @since  1.0.0
	 * @return bool
	 */
	public function permission_callback() {
		return current_user_can( 'manage_options' );
	}

	/**
	 * Example method.
	 *
	 * @since  1.0.0
	 * @param  WP_REST_Request  $request  Request object.
	 * @return WP_Error|WP_REST_Response
	 */
	public function example_method( $request ) {
		$response = new PLUGIN_CLASS_PREFIX_REST_Response();

		$success = false;

		try {
			$success = true;

			$response->status = '100';
			$response->data   = array();
		} catch ( Exception $e ) {
			$response->message = $e->getMessage();
		}

		$response->success = $success;
		$response->status  = $success ? '100' : '401';

		return rest_ensure_response( $response );
	}
}
