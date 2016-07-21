<?php
/**
 * Auto-loader for schema definitions.
 *
 * @package Schemify
 */

namespace Schemify\Schemas;

// Explicitly load Thing, since all other Schemas extend it.
require_once __DIR__ . '/schemas/Thing.php';

/**
 * Auto-load called schemas (if the definition exists).
 *
 * @param string $class The class name that has been called.
 */
function autoload( $class ) {
	$schema = substr( $class, strlen( __NAMESPACE__ ) );
	$file   = sprintf( '%s/schemas/%s.php', __DIR__, str_replace( '\\', '/', $schema ) );
	if ( file_exists( $file ) ) {
		require_once $file;
	}
}
spl_autoload_register( __NAMESPACE__ . '\autoload' );
