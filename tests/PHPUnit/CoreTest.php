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
		'schemas.php',
	);

	public function testBuildObject() {
		M::wpFunction( __NAMESPACE__ . '\get_schema_name', array(
			'times'  => 1,
			'args'   => array( 123, 'post' ),
			'return' => 'TestSchema',
		) );

		$this->assertInternalType( 'array', build_object( 123 ) );
	}

	public function testBuildObjectDefaultsToCurrentPostId() {
		M::wpFunction( 'get_the_ID', array(
			'times'  => 1,
			'return' => 123,
		) );

		M::wpFunction( __NAMESPACE__ . '\get_schema_name', array(
			'return' => 'TestSchema',
		) );

		build_object();
	}

	/**
	 * @expectedException PHPUnit_Framework_Error_Warning
	 */
	public function testBuildObjectDefaultsToThing() {
		M::wpFunction( __NAMESPACE__ . '\get_schema_name', array(
			'return' => 'SomeSchema',
		) );

		M::wpPassthruFunction( 'esc_attr' );
		M::wpPassthruFunction( 'esc_html' );
		M::wpPassthruFunction( '__' );

		$this->assertFalse(
			class_exists( 'Schemify\Schemas\SomeSchema' ),
			'This test does not expect SomeSchema to exist, recommend manual inspection'
		);

		$this->assertInstanceOf( 'Schemify\Schemas\Thing', build_object( 123 ) );
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

	public function testGetSchemaName() {
		M::wpFunction( 'get_post_type', array(
			'times'  => 1,
			'args'   => array( 123 ),
			'return' => 'post',
		) );

		M::onFilter( 'schemify_schema' )
			->with( 'Thing', 'post', 'post', 123 )
			->reply( 'SomeSchema' );

		$this->assertEquals( 'SomeSchema', get_schema_name( 123, 'post' ) );
	}

	public function testGetJson() {
		M::wpFunction( __NAMESPACE__ . '\build_object', array(
			'times'  => 1,
			'args'   => array( 123, 'post' ),
		) );

		M::wpFunction( 'wp_json_encode', array(
			'times'  => 1,
			'return' => '{"json":true}',
		) );

		M::wpPassthruFunction( 'wp_kses_post' );

		$expected  = '<script type="application/ld+json">' . PHP_EOL;
		$expected .= '{"json":true}' . PHP_EOL . '</script>';

		$this->expectOutputString( $expected );

		get_json( 123, 'post' );
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
