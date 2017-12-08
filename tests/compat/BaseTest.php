<?php
/**
 * Base test for compatibility files.
 *
 * @package Schemify
 */

namespace Test\Compat;

use InvalidArgumentException;
use WP_UnitTestCase;

abstract class BaseTest extends WP_UnitTestCase {

	/**
	 * The compatibility layer filename.
	 *
	 * @var string
	 */
	protected static $compatFile;

	/**
	 * Activate the plugin declared by self::$compatFile.
	 *
	 * @beforeClass
	 */
	public static function activatePlugin() {
		if ( ! static::$compatFile ) {
			throw new InvalidArgumentException( 'The $compatFile property has not been set for this test.' );
		}

		// Activate the corresponding plugin.
		$activated = activate_plugin( static::$compatFile, null, false, true );

		if ( is_wp_error( $activated ) ) {
			throw new InvalidArgumentException( 'Unable to activate plugin: ' . $activated->get_error_message() );
		}

		// Explicitly load the compatibility file.
		require_once dirname( dirname( __DIR__ ) ) . '/includes/compat/' . dirname( static::$compatFile ) . '.php';
	}

	/**
	 * Deactivate the plugin when we're done.
	 *
	 * @afterClass
	 */
	public static function deactivatePlugin() {
		deactivate_plugins( static::$compatFile, true );
	}

	public function testLoadedCompatibilityFile() {
		do_action( 'plugins_loaded' );

		$hook = 'schemify_load_compat_' . dirname( static::$compatFile );

		$this->assertGreaterThanOrEqual( 1, did_action( $hook ), sprintf( 'Did not see %s hook fire.', $hook ) );
	}
}
