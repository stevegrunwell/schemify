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

		M::wpFunction( 'get_the_excerpt', array(
			'times'  => 1,
			'args'   => array( 123 ),
			'return' => 'Excerpt',
		) );

		$this->assertEquals( 'Excerpt', $instance->getCaption( 123 ) );
	}

	public function testGetDescription() {
		$instance = new ImageObject( 123 );
		$post     = new \stdClass;
		$post->post_content = 'Content';

		M::wpFunction( 'get_post', array(
			'times'  => 1,
			'args'   => array( 123 ),
			'return' => $post,
		) );

		$this->assertEquals( 'Content', $instance->getDescription( 123 ) );
	}

	public function testGetExifData() {
		$this->markTestIncomplete();
	}

	public function testGetHeight() {
		$instance = new ImageObject( 123 );

		M::wpFunction( 'wp_get_attachment_image_src', array(
			'times'  => 1,
			'args'   => array( 123, 'full' ),
			'return' => array( 'URL', 800, 600 ),
		) );

		$this->assertEquals( 600, $instance->getHeight( 123 ) );
	}

	public function testGetHeightReturnsNullIfImageIsNotFound() {
		$instance = new ImageObject( 123 );

		M::wpFunction( 'wp_get_attachment_image_src', array(
			'times'  => 1,
			'args'   => array( 123, 'full' ),
			'return' => false,
		) );

		$this->assertNull( $instance->getHeight( 123 ) );
	}

	public function testGetImage() {
		$this->markTestIncomplete();
	}

	public function testGetWidth() {
		$instance = new ImageObject( 123 );

		M::wpFunction( 'wp_get_attachment_image_src', array(
			'times'  => 1,
			'args'   => array( 123, 'full' ),
			'return' => array( 'URL', 800, 600 ),
		) );

		$this->assertEquals( 800, $instance->getWidth( 123 ) );
	}

	public function testGetWidthReturnsNullIfImageIsNotFound() {
		$instance = new ImageObject( 123 );

		M::wpFunction( 'wp_get_attachment_image_src', array(
			'times'  => 1,
			'args'   => array( 123, 'full' ),
			'return' => false,
		) );

		$this->assertNull( $instance->getWidth( 123 ) );
	}
}
