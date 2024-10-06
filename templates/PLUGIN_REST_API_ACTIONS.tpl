		$this->loader->add_action( 'init', $plugin_public, 'rest_api_cors' );
		$this->loader->add_action( 'init', $plugin_public, 'rest_init' );

