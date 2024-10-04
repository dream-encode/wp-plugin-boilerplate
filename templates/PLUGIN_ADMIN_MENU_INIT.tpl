
	/**
	 * Adds menu pages.
	 *
	 * @since  1.0.0
	 * @return void
	 */
	public function add_menu_pages() {
		add_submenu_page(
			'options-general.php',
			__( 'PLUGIN_NAME', 'PLUGIN_SLUG' ),
			__( 'PLUGIN_NAME', 'PLUGIN_SLUG' ),
			'manage_options',
			'PLUGIN_SLUG-settings',
			array( $this, 'admin_settings_menu_callback' )
		);
	}

	/**
	 * Admin menu callback for the plugin settings page.
	 *
	 * @since  1.0.0
	 * @return void
	 */
	public function admin_settings_menu_callback() {
		echo '<div id="PLUGIN_SLUG-plugin-settings"></div>';
	}
