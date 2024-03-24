<?php
/**
 * Class PLUGIN_CLASS_PREFIX_REST_PickLists_Controller
 */

namespace PLUGIN_NAMESPACE\Core\RestApi\V1\Frontend;

use Exception;
use WP_REST_Server;
use WP_REST_Request;
use WP_REST_Response;
use WP_Error;
use WP_User;
use PLUGIN_NAMESPACE\Core\RestApi\PLUGIN_CLASS_PREFIX_REST_Response;
use PLUGIN_NAMESPACE\Core\Abstracts\PLUGIN_CLASS_PREFIX_Abstract_REST_Controller;


/**
 * Class PLUGIN_CLASS_PREFIX_REST_Users_Controller
 */
class PLUGIN_CLASS_PREFIX_REST_User_Controller extends PLUGIN_CLASS_PREFIX_Abstract_REST_Controller {
	/**
	 * PLUGIN_CLASS_PREFIX_REST_Users_Controller constructor.
	 */
	public function __construct() {
		$this->namespace = 'max-marine/v1';
		$this->rest_base = 'user';
	}

	/**
	 * Register routes API
	 *
	 * @since  1.0.0
	 * @return void
	 */
	public function register_routes() {
		$this->routes = array(
			'validate' => array(
				array(
					'methods'             => WP_REST_Server::CREATABLE,
					'callback'            => array( $this, 'validate_user' ),
					'permission_callback' => '__return_true',
				),
			),
		);

		parent::register_routes();
	}

	/**
	 * Validate a user with their login credentials.
	 *
	 * @since  1.0.0
	 * @param  WP_REST_Request  $request  Request object.
	 * @return WP_Error|WP_REST_Response
	 */
	public function validate_user( $request ) {
		$response = new PLUGIN_CLASS_PREFIX_REST_Response();

		$success = false;

		try {
			$username     = $request->get_param( 'u' );
			$app_password = $request->get_param( 'p' );

			$user = wp_authenticate_application_password( null, $username, $app_password );

			if ( ! $user instanceof WP_User ) {
				return rest_ensure_response(
					new WP_Error(
						'rest_forbidden_context',
						__( 'Sorry, authentication failed.', 'PLUGIN_SLUG' ),
						array( 'status' => rest_authorization_required_code() )
					)
				);
			}

			wp_clear_auth_cookie();
			wp_set_current_user( $user->ID );
			wp_set_auth_cookie( $user->ID );

			update_user_meta( $user->ID, 'PLUGIN_ABBR_last_login', current_time( 'mysql' ) );

			$success = true;

			$response->status = '100';
			$response->data   = $user;
		} catch ( Exception $e ) {
			$response->message = $e->getMessage();
		}

		$response->success = $success;
		$response->status  = $success ? '100' : '401';

		return rest_ensure_response( $response );
	}
}
