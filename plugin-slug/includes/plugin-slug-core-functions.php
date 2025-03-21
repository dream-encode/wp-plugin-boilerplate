<?php
/**
 * Common functions for the plugin.
 *
 * @link       https://dream-encode.com
 * @since      1.0.0
 *
 * @package    PLUGIN_CLASS_PREFIX
 * @subpackage PLUGIN_CLASS_PREFIX/includes
 */

/**
 * Define a constant if it is not already defined.
 *
 * @since  1.0.0
 * @param  string  $name   Constant name.
 * @param  mixed   $value  Constant value.
 * @return void
 */
function PLUGIN_FUNC_PREFIX_maybe_define_constant( $name, $value ) {
	if ( ! defined( $name ) ) {
		define( $name, $value );
	}
}

/**
 * Get a plugin setting by key.
 *
 * @since  1.0.0
 * @param  string  $key      Setting key.
 * @param  mixed   $default  Optional. Default value. Default false.
 * @return mixed
 */
function PLUGIN_FUNC_PREFIX_get_plugin_setting( $key, $default = false ) {
	static $settings = false;

	if ( false === $settings ) {
		$settings = get_option( 'PLUGIN_FUNC_PREFIX_plugin_settings', array() );
	}

	if ( isset( $settings[ $key ] ) ) {
		return $settings[ $key ];
	}

	return $default;
}

/**
 * Get an array of data that relates enqueued assets to specific admin screens.
 *
 * @since  1.0.0
 * @return array
 */
function PLUGIN_FUNC_PREFIX_get_admin_screens_to_assets() {
	return array(
		'settings_page_PLUGIN_SLUG-settings' => array(
			array(
				'name'         => 'settings-page',
				'localization' => array(
					'REST_URL'    => get_rest_url( null, '' ),
					'WP_REST_URL' => get_rest_url(),
					'NONCES'      => array(
						'REST' => wp_create_nonce( 'wp_rest' ),
					),
					'SETTINGS'    => get_option( 'PLUGIN_FUNC_PREFIX_plugin_settings', array() ),
				),
			),
		),
	);
}

/**
 * Get a list of WP style dependencies.
 *
 * @since  1.0.0
 * @return string[]
 */
function PLUGIN_FUNC_PREFIX_get_wp_style_dependencies() {
	return array(
		'wp-components',
	);
}

/**
 * Get a list of WP style dependencies.
 *
 * @since  1.0.0
 * @param  array  $dependencies  Raw dependencies.
 * @return string[]
 */
function PLUGIN_FUNC_PREFIX_get_style_asset_dependencies( $dependencies ) {
	$style_dependencies = PLUGIN_FUNC_PREFIX_get_wp_style_dependencies();

	$new_dependencies = array();

	foreach ( $dependencies as $dependency ) {
		if ( in_array( $dependency, $style_dependencies, true ) ) {
			$new_dependencies[] = $dependency;
		}
	}

	return $new_dependencies;
}

/**
 * Get enqueued assets for the current admin screen.
 *
 * @since  1.0.0
 * @return array
 */
function PLUGIN_FUNC_PREFIX_admin_current_screen_enqueued_assets() {
	$current_screen = get_current_screen();

	if ( ! $current_screen instanceof WP_Screen ) {
		return array();
	}

	$assets = PLUGIN_FUNC_PREFIX_get_admin_screens_to_assets();

	return ! empty( $assets[ $current_screen->id ] ) ? $assets[ $current_screen->id ] : array();
}

/**
 * Check if the current admin screen has any enqueued assets.
 *
 * @since  1.0.0
 * @return int
 */
function PLUGIN_FUNC_PREFIX_admin_current_screen_has_enqueued_assets() {
	return count( PLUGIN_FUNC_PREFIX_admin_current_screen_enqueued_assets() );
}

/**
 * Get enqueued assets for the an admin screen.
 *
 * @since  1.0.0
 * @param  WP_Screen  $screen  Screen to check.
 * @return array
 */
function PLUGIN_FUNC_PREFIX_admin_screen_enqueued_assets( $screen ) {
	if ( ! $screen instanceof WP_Screen ) {
		return array();
	}

	$assets = PLUGIN_FUNC_PREFIX_get_admin_screens_to_assets();

	return ! empty( $assets[ $screen->id ] ) ? $assets[ $screen->id ] : array();
}

/**
 * Maybe enqueue media, if it hasn't been enqueued already.
 *
 * @since  1.0.0
 * @param  array  $asset  Current asset being checked.
 * @return void
 */
function PLUGIN_FUNC_PREFIX_maybe_enqueue_media( $asset ) {
	static $enqueued_media = false;

	if ( false === $enqueued_media && ! empty( $asset['enqueue_media'] ) ) {
		wp_enqueue_media();

		$enqueued_media = true;
	}
}

/**
 * Check if an admin screen has any enqueued assets.
 *
 * @since  1.0.0
 * @param  WP_Screen  $screen  Screen to check.
 * @return int
 */
function PLUGIN_FUNC_PREFIX_admin_screen_has_enqueued_assets( $screen ) {
	return count( PLUGIN_FUNC_PREFIX_admin_screen_enqueued_assets( $screen ) );
}
