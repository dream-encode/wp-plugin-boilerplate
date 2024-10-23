/**
	 * Send the CORS header on REST requests.
	 *
	 * @since  1.0.0
	 * @return void
	 */
	public function rest_api_cors() {
		if ( 'production' === wp_get_environment_type() ) {
			return;
		}

		header( 'Access-Control-Allow-Origin: *' );
	}

	/**
	 * Initialize rest api instances.
	 *
	 * @since  1.0.0
	 * @return void
	 */
	public function rest_init() {
		$api = new PLUGIN_CLASS_PREFIX_Core_API();
	}
