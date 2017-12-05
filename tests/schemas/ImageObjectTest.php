<?php
/**
 * Tests for the ImageObject schema.
 *
 * @package Schemify
 */

namespace Test\Schemas;

use Schemify\Schemas\ImageObject;
use WP_UnitTestCase;

class ImageObjectTest extends WP_UnitTestCase {

	public function testGetCaption() {
		$post  = $this->factory()->attachment->create( [
			'post_excerpt' => 'This is the excerpt.',
		] );
		$image = new ImageObject( $post );

		$this->assertEquals( 'This is the excerpt.', $image->getCaption( $post ) );
	}

	public function testGetDescription() {
		$post  = $this->factory()->attachment->create_and_get();
		$image = new ImageObject( $post->ID );

		$this->assertEquals( $post->post_content, $image->getDescription( $post->ID ) );
	}

	public function testGetExifData() {
		$post  = $this->factory()->attachment->create();
		$image = new ImageObject( $post, true );

		add_filter( 'wp_get_attachment_metadata', function () {
			return [
				'image_meta' => [
					'meta1' => 'value1',
					'meta2' => 'value2',
				],
			];
		} );

		$this->assertEquals( [
			[
				'@type' => 'PropertyValue',
				'name'  => 'meta1',
				'value' => 'value1',
			],
			[
				'@type' => 'PropertyValue',
				'name'  => 'meta2',
				'value' => 'value2',
			],
		], $image->getExifData( $post ) );
	}

	public function testGetExifDataIsNullIfNotMainSchema() {
		$image = new ImageObject( 123 );

		$this->assertNull( $image->getExifData( 123 ) );
	}

	public function testGetExifDataStripsEmptyMetaValues() {
		$post  = $this->factory()->attachment->create();
		$image = new ImageObject( $post, true );

		add_filter( 'wp_get_attachment_metadata', function () {
			return [
				'image_meta' => [
					'meta1' => '',
				],
			];
		} );

		$this->assertEmpty( $image->getExifData( $post ) );
	}

	public function testGetHeight() {
		$post  = $this->factory()->attachment->create();
		$image = new ImageObject( $post );

		add_filter( 'wp_get_attachment_image_src', function () {
			return [ 'URL', 800, 600 ];
		} );

		$this->assertEquals( 600, $image->getHeight( $post ) );
	}

	public function testGetHeightReturnsNullIfImageIsNotFound() {
		$image = new ImageObject( 123 );

		$this->assertNull( $image->getHeight( 123 ) );
	}

	/**
	 * There used to be an explicit getImage() method on the ImageObject schema that simply returned
	 * null. This test persists to verify that the $removeProperties array is working.
	 */
	public function testGetImage() {
		$image = new ImageObject( 123 );

		$this->assertNull(
			$image->getImage( 123 ),
			'An ImageObject inside an ImageObject? Are you mad?!'
		);
	}

	public function testGetWidth() {
		$post  = $this->factory()->attachment->create();
		$image = new ImageObject( $post );

		add_filter( 'wp_get_attachment_image_src', function () {
			return [ 'URL', 800, 600 ];
		} );

		$this->assertEquals( 800, $image->getWidth( $post ) );
	}

	public function testGetWidthReturnsNullIfImageIsNotFound() {
		$image = new ImageObject( 123 );

		$this->assertNull( $image->getWidth( 123 ) );
	}
}
