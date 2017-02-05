<?php
/**
 * Compatibility with third-party plugins.
 *
 * @package Schemify
 */

namespace Schemify\Compat;

/**
 * Attempt to load compatibility files for currently-active plugins.
 *
 * This function will get a list of all active plugins' slugs and, if a match exists within
 * includes/compat/, load that file.
 */
function load_compat_files() {
	$plugins = get_option( 'active_plugins', array() );
	$compat  = __DIR__ . '/compat/';

	foreach ( $plugins as $plugin ) {
		$slug = dirname( $plugin );
		$file = $compat . $slug . '.php';
		$load = file_exists( $file );

		/**
		 * Determine whether or not Schemify should load the compatibility file for plugin $slug.
		 *
		 * @param bool   $load   Whether or not the compatibility file for $slug be loaded.
		 * @param string $plugin The plugin's slug.
		 */
		$load = apply_filters( 'schemify_should_load_compat_' . $slug, $load, $slug );

		// Either no compatibility file exists or someone has canceled it's loading.
		if ( ! $load ) {
			continue;
		}

		include_once $file;

		/**
		 * Fires after the compatibility layer for $slug has been loaded.
		 *
		 * @param string $plugin The plugin's slug.
		 */
		do_action( 'schemify_load_compat_', $slug );
	}
}
add_action( 'init', __NAMESPACE__ . '\load_compat_files' );
