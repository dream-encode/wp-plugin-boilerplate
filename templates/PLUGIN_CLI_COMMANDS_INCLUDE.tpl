/**
		 * If this is a WP_CLI request, include the commands.
		 */
		if ( defined( 'WP_CLI' ) && WP_CLI ) {
			include_once PLUGIN_DEFINE_PREFIX_PLUGIN_PATH . 'includes/cli/class-PLUGIN_SLUG-cli-commands.php';
		}

