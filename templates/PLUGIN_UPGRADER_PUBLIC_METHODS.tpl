/**
	 * Do stuff when plugin updates happen.
	 *
	 * @since  1.0.0
	 * @param  object  $upgrader_object  Upgrader object.
	 * @param  array   $options          Options.
	 * @return void
	 */
	public function upgrader_process_complete( $upgrader_object, $options ) {
		if ( isset( $options['plugins'] ) && is_array( $options['plugins'] ) ) {
			foreach ( $options['plugins'] as $index => $plugin ) {
				if ( 'PLUGIN_SLUG/PLUGIN_SLUG.php' === $plugin ) {
					as_enqueue_async_action( 'PLUGIN_FUNC_PREFIX_process_plugin_upgrade', array(), 'PLUGIN_SLUG' );
					return;
				}
			}
		}
	}

	/**
	 * Maybe perform database migrations when a plugin upgrade occurs.
	 *
	 * @since  1.0.0
	 * @return void
	 */
	public function process_plugin_upgrade() {
		$upgrader = new PLUGIN_CLASS_PREFIX_Upgrader();
	}
