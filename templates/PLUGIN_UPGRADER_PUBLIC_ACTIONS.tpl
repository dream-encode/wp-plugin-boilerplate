$this->loader->add_action( 'upgrader_process_complete', $plugin_public, 'upgrader_process_complete', 10, 2 );

		$this->loader->add_action( 'PLUGIN_FUNC_PREFIX_process_plugin_upgrade', $plugin_public, 'process_plugin_upgrade', 10, 2 );
