<?php
/**
 * Tests for the plugin's core functionality.
 *
 * @package Schemify
 */

namespace Schemify\Core;

use WP_Mock as M;
use Schemify;

class CoreTest extends Schemify\TestCase {

	protected $testFiles = array(
		'core.php',
	);

	public function testBuildObject() {
		$this->markTestIncomplete();
	}

	public function testGetJson() {
		M::wpFunction( __NAMESPACE__ . '\build_object', array(
			'times'  => 1,
			'args'   => array( 123 ),
		) );

		M::wpFunction( 'wp_json_encode', array(
			'times'  => 1,
			'return' => '{"json":true}',
		) );

		M::wpPassthruFunction( 'wp_kses_post' );

		$expected  = '<script type="application/ld+json">' . PHP_EOL;
		$expected .= '{"json":true}' . PHP_EOL . '</script>';

		$this->expectOutputString( $expected );

		get_json( 123 );
	}
}
