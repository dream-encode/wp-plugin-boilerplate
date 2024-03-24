<?php
/**
 * Default filters for the plugin.
 *
 * @link       https://dream-encode.com
 * @since      1.0.0
 *
 * @package    PLUGIN_CLASS_PREFIX
 */

namespace PLUGIN_NAMESPACE\Core;

add_action( 'PLUGIN_HOOK_PREFIX/example_action', 'PLUGIN_NAMESPACE\Frontend\example_function', 10, 3 );
