<?php
/**
 * Tests for the CreativeWork schema.
 *
 * @package Schemify
 */

namespace Test\Schemas;

use Schemify\Schemas\CreativeWork;
use Schemify\Schemas\Organization;
use Schemify\Schemas\Person;
use WP_UnitTestCase;

class CreativeWorkTest extends WP_UnitTestCase {

	public function testGetAuthor() {
		$post  = $this->factory()->post->create();
		$media = new CreativeWork( $post, true );

		$this->assertInstanceOf( Person::class, $media->getAuthor( $post ) );
	}

	public function testGetAuthorReturnsNullIfNotMain() {
		$media = new CreativeWork( 123, false );

		$this->assertNull( $media->getAuthor( 123 ) );
	}

	public function testGetAuthorReturnsNullIfPostNotFound() {
		$media = new CreativeWork( 123, true );

		$this->assertNull( $media->getAuthor( 123 ) );
	}

	public function testGetDateCreated() {
		$post  = $this->factory()->post->create_and_get();
		$media = new CreativeWork( $post->ID );

		$this->assertEquals(
			date( 'c', strtotime( $post->post_date ) ),
			$media->getDateCreated( $post->ID )
		);
	}

	public function testGetDateModified() {
		$post  = $this->factory()->post->create_and_get();
		$media = new CreativeWork( $post->ID );

		$this->assertEquals(
			date( 'c', strtotime( $post->post_modified ) ),
			$media->getDateModified( $post->ID )
		);
	}

	public function testGetDatePublished() {
		$post  = $this->factory()->post->create_and_get();
		$media = new CreativeWork( $post->ID );

		$this->assertEquals(
			$media->getDateCreated( $post->ID ),
			$media->getDatePublished( $post->ID )
		);
	}

	public function testGetHeadline() {
		$post  = $this->factory()->post->create_and_get();
		$media = new CreativeWork( $post->ID );

		$this->assertEquals( $media->getName( $post->ID ), $media->getHeadline( $post->ID ) );
	}

	public function testGetPublisher() {
		$media = new CreativeWork( 123, true );

		$this->assertInstanceOf( Organization::class, $media->getPublisher( 1 ) );
	}

	public function testGetPublisherIsNullWhenNotMainSchema() {
		$media = new CreativeWork( 123, false );

		$this->assertNull( $media->getPublisher( 1 ) );
	}

	public function testGetThumbnailUrl() {
		$post       = $this->factory()->post->create();
		$attachment = $this->factory()->attachment->create();
		$media      = new CreativeWork( 123 );

		set_post_thumbnail( $post, $attachment );

		add_filter( 'wp_get_attachment_image_src', function () {
			return [ 'https://example.com/image.jpg', 150, 150 ];
		} );

		$this->assertEquals(
			'https://example.com/image.jpg',
			$media->getThumbnailUrl( $attachment )
		);
	}
}
