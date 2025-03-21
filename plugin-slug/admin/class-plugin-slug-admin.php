<?php
/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://dream-encode.com
 * @since      1.0.0
 *
 * @package    PLUGIN_CLASS_PREFIX
 * @subpackage PLUGIN_CLASS_PREFIX/admin
 */

namespace PLUGIN_NAMESPACE\Admin;

use WP_Screen;
PLUGIN_LIST_TABLE_INCLUDE;

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    PLUGIN_CLASS_PREFIX
 * @subpackage PLUGIN_CLASS_PREFIX/admin
 * @author     David Baumwald <david@dream-encode.com>
 */
class PLUGIN_CLASS_PREFIX_Admin {

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since  1.0.0
	 * @return void
	 */
	public function enqueue_styles() {
		if ( ! PLUGIN_FUNC_PREFIX_admin_current_screen_has_enqueued_assets() ) {
			return;
		}

		$current_screen = get_current_screen();

		if ( ! $current_screen instanceof WP_Screen ) {
			return;
		}

		$screens_to_assets = PLUGIN_FUNC_PREFIX_get_admin_screens_to_assets();

		foreach ( $screens_to_assets as $screen => $assets ) {
			if ( $current_screen->id !== $screen ) {
				continue;
			}

			foreach ( $assets as $asset ) {
				$asset_base_url = PLUGIN_DEFINE_PREFIX_PLUGIN_URL . 'admin/';

				$asset_file = include( PLUGIN_DEFINE_PREFIX_PLUGIN_PATH . "admin/assets/dist/js/admin-{$asset['name']}.min.asset.php" );

				wp_enqueue_style(
					"PLUGIN_SLUG-admin-{$asset['name']}",
					$asset_base_url . "assets/dist/css/admin-{$asset['name']}.min.css",
					PLUGIN_FUNC_PREFIX_get_style_asset_dependencies( $asset_file['dependencies'] ),
					$asset_file['version'],
					'all'
				);
			}
		}
	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since  1.0.0
	 * @return void
	 */
	public function enqueue_scripts() {
		if ( ! PLUGIN_FUNC_PREFIX_admin_current_screen_has_enqueued_assets() ) {
			return;
		}

		$current_screen = get_current_screen();

		if ( ! $current_screen instanceof WP_Screen ) {
			return;
		}

		$screens_to_assets = PLUGIN_FUNC_PREFIX_get_admin_screens_to_assets();

		foreach ( $screens_to_assets as $screen => $assets ) {
			if ( $current_screen->id !== $screen ) {
				continue;
			}

			foreach ( $assets as $asset ) {
				if ( isset( $asset['conditions'] ) && false === $asset['conditions'] ) {
					continue;
				}

				$asset_base_url = PLUGIN_DEFINE_PREFIX_PLUGIN_URL . 'admin/';

				$asset_file = include( PLUGIN_DEFINE_PREFIX_PLUGIN_PATH . "admin/assets/dist/js/admin-{$asset['name']}.min.asset.php" );

				PLUGIN_FUNC_PREFIX_maybe_enqueue_media( $asset );

				wp_register_script(
					"PLUGIN_SLUG-admin-{$asset['name']}",
					$asset_base_url . "assets/dist/js/admin-{$asset['name']}.min.js",
					$asset['dependencies'] ? array_merge( $asset_file['dependencies'], $asset['dependencies'] ) : $asset_file['dependencies'],
					$asset_file['version'],
					array(
						'in_footer' => true,
					)
				);

				if ( ! empty( $asset['localization'] ) ) {
					wp_localize_script( "PLUGIN_SLUG-admin-{$asset['name']}", 'PLUGIN_SHORT_DEFINE_PREFIX', $asset['localization'] );
				}

				wp_enqueue_script( "PLUGIN_SLUG-admin-{$asset['name']}" );

				wp_set_script_translations( "PLUGIN_SLUG-admin-{$asset['name']}", 'PLUGIN_SLUG' );
			}
		}
	}
	PLUGIN_ADMIN_MENU_INIT;
}
