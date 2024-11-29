<?php
/**
 * Class PLUGIN_CLASS_PREFIX_REST_Authentication.
 *
 * @since 1.0.0
 */

namespace PLUGIN_NAMESPACE\Core\RestApi;

use WP_Error;
use WP_User;

/**
 * Class PLUGIN_CLASS_PREFIX_REST_Authentication.
 *
 * @since 1.0.0
 */
class PLUGIN_CLASS_PREFIX_REST_Authentication {
	/**
	 * The current nonce.
	 *
	 * @since   1.0.0
	 * @access  protected
	 * @var     string   $wp_rest_nonce  The current nonce.
	 */
	protected static $wp_rest_nonce = '';

	/**
	 * The current user id.
	 *
	 * @since   1.0.0
	 * @access  protected
	 * @var     int   $wp_current_user_id  The current user id.
	 */
	protected static $wp_current_user_id = 0;

	/**
	 * Constructor.
	 */
	public function __construct() {
		add_filter( 'rest_authentication_errors', array( $this, 'authentication_fallback' ), 9999999 );
	}

	/**
	 * Check the cookie for errors.
	 *
	 * @since  1.0.0
	 * @param  WP_Error|null|true  $result  Current result.
	 * @return mixed
	 */
	public function rest_cookie_check_errors( $result ) {
		if ( ! empty( $result ) ) {
			return $result;
		}

		if ( is_user_logged_in() ) {
			self::$wp_rest_nonce      = wp_create_nonce( 'wp_rest' );
			self::$wp_current_user_id = (int) get_current_user_id();
		}

		return $result;
	}

	/**
	 * Authenticate the user if authentication wasn't performed during the
	 * determine_current_user action.
	 *
	 * Necessary in cases where wp_get_current_user() is called before WooCommerce is loaded.
	 *
	 * @see https://github.com/woocommerce/woocommerce/issues/16847
	 *
	 * @since  1.0.0
	 * @param  WP_Error|null|bool  $error  Error data.
	 * @return WP_Error|null|bool
	 */
	public function authentication_fallback( $error ) {
		if ( 0 === get_current_user_id() ) {
			// Authentication hasn't occurred during `determine_current_user`, so check auth.
			$user_id = $this->authenticate( false );

			if ( $user_id ) {
				wp_set_current_user( $user_id );

				return true;
			}
		}

		return $error;
	}

	/**
	 * Authenticate user.
	 *
	 * @since  1.0.0
	 * @param  int|false  $user_id  User ID if one has been determined, false otherwise.
	 * @return int|false
	 */
	public function authenticate( $user_id ) {
		// Do not authenticate twice and check if is a request to our endpoint in the WP REST API.
		if ( ! empty( $user_id ) || ! $this->is_rest_api_request() ) {
			return $user_id;
		}

		if ( is_ssl() ) {
			$user_id = $this->authenticate_app_password();
		}

		if ( $user_id ) {
			return $user_id;
		}

		return false;
	}

	/**
	 * Check if request is rest api.
	 *
	 * @since  1.0.0
	 * @return mixed
	 */
	public function authenticate_app_password() {
		if ( ! isset( $_SERVER['PHP_AUTH_USER'], $_SERVER['PHP_AUTH_PW'] ) ) {
			return false;
		}

		$user = wp_unslash( $_SERVER['PHP_AUTH_USER'] );
		$pass = wp_unslash( $_SERVER['PHP_AUTH_PW'] );

		$authenticated = wp_authenticate_application_password( null, $user, $pass );

		if ( $authenticated instanceof WP_User ) {
			return $authenticated->ID;
		}

		return false;
	}

	/**
	 * Check if request is rest api.
	 *
	 * @since  1.0.0
	 * @return bool
	 */
	public function is_rest_api_request() {
		if ( empty( $_SERVER['REQUEST_URI'] ) ) {
			return false;
		}

		$rest_prefix = trailingslashit( rest_get_url_prefix() );
		$request_uri = esc_url_raw( wp_unslash( $_SERVER['REQUEST_URI'] ) );

		return false !== strpos( $request_uri, $rest_prefix . 'PLUGIN_REST_NAMESPACE/' );
	}

	/**
	 * Get current nonce.
	 *
	 * @since  1.0.0
	 * @return string
	 */
	public static function get_wp_rest_nonce() {
		return self::$wp_rest_nonce;
	}

	/**
	 * Get the current user id.
	 *
	 * @since  1.0.0
	 * @return int
	 */
	public static function get_wp_user_id() {
		return self::$wp_current_user_id;
	}

	/**
	 * Check permission of a user on a post type.
	 *
	 * @since  1.0.0
	 * @param  string  $post_type   Post toye to check.
	 * @param  string  $permission  What context.
	 * @param  int     $user_id     User to validate.
	 * @return bool
	 */
	public static function check_post_permissions( $post_type, $permission = 'read', $user_id = 0 ) {
		if ( ! $user_id ) {
			$user_id = self::get_wp_user_id();
		}

		$permissions = array(
			'read'   => 'read_private_posts',
			'create' => 'publish_posts',
			'edit'   => 'edit_post',
			'delete' => 'delete_post',
			'batch'  => 'edit_others_posts',
		);

		if ( 'revision' === $post_type ) {
			$user_permission = false;
		} else {
			$cap              = $permissions[ $permission ];
			$post_type_object = get_post_type_object( $post_type );

			if ( ! $post_type_object ) {
				return false;
			}

			$user_permission = current_user_can( $post_type_object->cap->$cap, $user_id );
		}

		return $user_permission;
	}

	/**
	 * Check permission if user is logged in.
	 *
	 * @since  1.0.0
	 * @return bool
	 */
	public static function check_logged_in_permission() {
		return (bool) self::get_wp_user_id();
	}

	/**
	 * Check permission if user is administrator.
	 *
	 * @since  1.0.0
	 * @return bool
	 */
	public static function check_admin_permission() {
		return current_user_can( 'manage_options' );
	}

	/**
	 * Check permission if user is editor.
	 *
	 * @since  1.0.0
	 * @return bool
	 */
	public static function check_editor_permission() {
		return current_user_can( 'edit_posts' );
	}

	/**
	 * Check permission if user is shop_manager.
	 *
	 * @since  1.0.0
	 * @return bool
	 */
	public static function check_woocommerce_shop_manager_permission() {
		return current_user_can( 'manage_woocommerce' );
	}
}

return new PLUGIN_CLASS_PREFIX_REST_Authentication();
