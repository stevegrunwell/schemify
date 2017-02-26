<?php
/**
 * Tests for the ImageObject schema.
 *
 * @package Schemify
 */

namespace Schemify\Schemas;

use WP_Mock as M;

use Mockery;
use ReflectionMethod;
use ReflectionProperty;
use Schemify;

class ImageObjectTest extends Schemify\TestCase {

	protected $testFiles = array(
		'schemas.php',
	);

	public function testGetCaption() {
		$instance = new ImageObject( 123 );

		M::userFunction( 'get_post_field', array(
			'times'  => 1,
			'args'   => array( 'post_excerpt', 123, 'js' ),
			'return' => 'Excerpt',
		) );

		$this->assertEquals( 'Excerpt', $instance->getCaption( 123 ) );
	}

	public function testGetDescription() {
		$instance = new ImageObject( 123 );
		$post     = new \stdClass;
		$post->post_content = 'Content';

		M::userFunction( 'get_post', array(
			'times'  => 1,
			'args'   => array( 123 ),
			'return' => $post,
		) );

		$this->assertEquals( 'Content', $instance->getDescription( 123 ) );
	}

	public function testGetExifData() {
		$instance = new ImageObject( 123, true );

		M::userFunction( 'wp_get_attachment_metadata', array(
			'times'  => 1,
			'args'   => array( 123 ),
			'return' => array(
				'image_meta' => array(
					'meta1' => 'value1',
					'meta2' => 'value2',
				),
			),
		) );

		$this->assertEquals( array(
			array(
				'@type' => 'PropertyValue',
				'name'  => 'meta1',
				'value' => 'value1',
			),
			array(
				'@type' => 'PropertyValue',
				'name'  => 'meta2',
				'value' => 'value2',
			),
		), $instance->getExifData( 123 ) );
	}

	public function testGetExifDataIsNullIfNotMainSchema() {
		$instance = new ImageObject( 123 );

		$this->assertNull( $instance->getExifData( 123 ) );
	}

	public function testGetExifDataStripsEmptyMetaValues() {
		$instance = new ImageObject( 123, true );

		M::userFunction( 'wp_get_attachment_metadata', array(
			'times'  => 1,
			'args'   => array( 123 ),
			'return' => array(
				'image_meta' => array(
					'meta1' => '',
				),
			),
		) );

		$this->assertEmpty( $instance->getExifData( 123 ) );
	}

	public function testGetHeight() {
		$instance = new ImageObject( 123 );

		M::userFunction( 'wp_get_attachment_image_src', array(
			'times'  => 1,
			'args'   => array( 123, 'full' ),
			'return' => array( 'URL', 800, 600 ),
		) );

		$this->assertEquals( 600, $instance->getHeight( 123 ) );
	}

	public function testGetHeightReturnsNullIfImageIsNotFound() {
		$instance = new ImageObject( 123 );

		M::userFunction( 'wp_get_attachment_image_src', array(
			'times'  => 1,
			'args'   => array( 123, 'full' ),
			'return' => false,
		) );

		$this->assertNull( $instance->getHeight( 123 ) );
	}

	/**
	 * There used to be an explicit getImage() method on the ImageObject schema that simply returned
	 * null. This test persists to verify that the $removeProperties array is working.
	 */
	public function testGetImage() {
		$instance = new ImageObject( 123 );

		$this->assertNull(
			$instance->getImage( 123 ),
			'An ImageObject inside an ImageObject? Are you mad?!'
		);
	}

	public function testGetWidth() {
		$instance = new ImageObject( 123 );

		M::userFunction( 'wp_get_attachment_image_src', array(
			'times'  => 1,
			'args'   => array( 123, 'full' ),
			'return' => array( 'URL', 800, 600 ),
		) );

		$this->assertEquals( 800, $instance->getWidth( 123 ) );
	}

	public function testGetWidthReturnsNullIfImageIsNotFound() {
		$instance = new ImageObject( 123 );

		M::userFunction( 'wp_get_attachment_image_src', array(
			'times'  => 1,
			'args'   => array( 123, 'full' ),
			'return' => false,
		) );

		$this->assertNull( $instance->getWidth( 123 ) );
	}
}
