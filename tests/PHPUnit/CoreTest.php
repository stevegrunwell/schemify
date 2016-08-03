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

	public function testGetAttachmentTypeWithImages() {
		$mimes = array( 'image/jpeg', 'image/png', 'image/gif' );
		M::wpFunction( 'get_post_mime_type', array(
			'times'           => count( $mimes ),
			'args'            => array( 123 ),
			'return_in_order' => $mimes,
		) );

		foreach ( $mimes as $mime ) {
			$this->assertEquals(
				'image',
				get_attachment_type( 123 ),
				sprintf( 'The %s MIME type should register as an image', $mime )
			);
		}
	}

	public function testGetAttachmentTypeWithAudio() {
		$mimes = array( 'audio/mp3', 'audio/wav' );
		M::wpFunction( 'get_post_mime_type', array(
			'times'           => count( $mimes ),
			'args'            => array( 123 ),
			'return_in_order' => $mimes,
		) );

		foreach ( $mimes as $mime ) {
			$this->assertEquals(
				'audio',
				get_attachment_type( 123 ),
				sprintf( 'The %s MIME type should register as an audio file', $mime )
			);
		}
	}

	public function testGetAttachmentTypeWithVideo() {
		$mimes = array( 'video/mp4', 'video/mov' );
		M::wpFunction( 'get_post_mime_type', array(
			'times'           => count( $mimes ),
			'args'            => array( 123 ),
			'return_in_order' => $mimes,
		) );

		foreach ( $mimes as $mime ) {
			$this->assertEquals(
				'video',
				get_attachment_type( 123 ),
				sprintf( 'The %s MIME type should register as a video', $mime )
			);
		}
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

	public function testStripNamespace() {
		$this->assertEquals( 'Baz', strip_namespace( 'Foo\Bar\Baz' ) );
		$this->assertEquals( 'Baz', strip_namespace( '\Foo\Bar\Baz' ) );
	}

	public function testStripNamespaceWithNoNamespace() {
		$this->assertEquals( 'Baz', strip_namespace( 'Baz' ) );
		$this->assertEquals( 'Baz', strip_namespace( '\Baz' ) );
	}
}
