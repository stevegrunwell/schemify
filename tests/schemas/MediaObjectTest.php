<?php
/**
 * Tests for the MediaObject schema.
 *
 * @package Schemify
 */

namespace Test\Schemas;

use Schemify\Schemas\CreativeWork;
use Schemify\Schemas\MediaObject;
use WP_UnitTestCase;

class MediaObjectTest extends WP_UnitTestCase {

	public function testGetAssociatedArticle() {
		$post       = $this->factory()->post->create();
		$attachment = $this->factory()->attachment->create( [
			'post_parent' => $post,
		] );
		$media      = new MediaObject( $attachment, true );

		$this->assertInstanceOf( CreativeWork::class, $media->getAssociatedArticle( $attachment ) );
	}

	public function testGetAssociatedArticleReturnsNullIfNotMain() {
		$media = new MediaObject( 123, false );

		$this->assertNull( $media->getAssociatedArticle( 123 ) );
	}

	/**
	 * @expectedException PHPUnit_Framework_Error_Warning
	 */
	public function testGetAssociatedArticleHandlesMissingSchemas() {
		$post       = $this->factory()->post->create();
		$attachment = $this->factory()->attachment->create( [
			'post_parent' => $post,
		] );
		$media      = new MediaObject( $attachment, true );

		add_filter( 'schemify_schema', function () {
			return 'ThisIsNotAClass';
		} );

		$this->assertNull( $media->getAssociatedArticle( $attachment ) );
	}

	public function testGetAssociatedArticleIfMediaIsUnattached() {
		$attachment = $this->factory()->attachment->create();
		$media      = new MediaObject( $attachment, true );

		$this->assertNull( $media->getAssociatedArticle( $attachment ) );
	}

	public function testGetContentUrl() {
		$post  = $this->factory()->attachment->create();
		$media = new MediaObject( $post );

		add_filter( 'wp_get_attachment_url', function () {
			return 'https://example.com/foo';
		} );

		$this->assertEquals( 'https://example.com/foo', $media->getContentUrl( $post ) );
	}

	public function testGetThumbnailUrl() {
		$post  = $this->factory()->attachment->create();
		$media = new MediaObject( $post );

		add_filter( 'wp_get_attachment_image_src', function () {
			return [ 'https://example.com/foo.jpg', 150, 150 ];
		} );

		$this->assertEquals( 'https://example.com/foo.jpg', $media->getThumbnailUrl( $post ) );
	}

	public function testGetThumbnailUrlReturnsNullIfThumbnailIsNotFound() {
		$media = new MediaObject( 123 );

		add_filter( 'wp_get_attachment_image_src', function () {
			return false;
		} );

		$this->assertNull( $media->getThumbnailUrl( 123 ) );
	}
}
