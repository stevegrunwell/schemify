<?php
/**
 * Tests for the core functionality.
 *
 * @package Schemify
 */

namespace Test;

use Schemify\Core as Core;
use Schemify\Schemas\ImageObject;
use Schemify\Schemas\MediaObject;
use WP_UnitTestCase;

class CoreTest extends WP_UnitTestCase {

	public function testBuildObject() {
		$post = $this->factory()->post->create_and_get();

		$response = Core\build_object( $post->ID );

		$this->assertEquals( 'http://schema.org', $response['@context'] );
		$this->assertEquals( $post->post_title, $response['name'] );
	}

	public function testBuildObjectDefaultsToCurrentPost() {
		$post = $this->factory()->post->create_and_get();

		$this->go_to( get_permalink( $post->ID ) );

		$response = Core\build_object();

		$this->assertEquals( $post->post_title, $response['name'] );
	}

	public function testBuildObjectHandlesMissingSchemaClasses() {
		$post = $this->factory()->post->create_and_get();
		add_filter( 'schemify_schema', function () {
			return 'NonExistentSchema';
		} );

		$response = @Core\build_object( $post->ID, 'non-existent item' );

		$this->assertEquals( 'Thing', $response['@type'] );
	}

	/**
	 * @dataProvider attachmentTypeProvider()
	 */
	public function testGetAttachmentType( $mime, $expected ) {
		$attachment = self::factory()->attachment->create_object( 'image.mp4', 0, [
			'post_mime_type' => $mime,
		] );

		$this->assertEquals( $expected, Core\get_attachment_type( $attachment ) );
	}

	public function attachmentTypeProvider() {
		return [
			'JPEG' => [ 'image/jpeg', 'image' ],
			'PNG'  => [ 'image/png', 'image' ],
			'GIF'  => [ 'image/gif', 'image' ],
			'MP3'  => [ 'audio/mp3', 'audio' ],
			'WAV'  => [ 'audio/wav', 'audio' ],
			'MP4'  => [ 'video/mp4', 'video' ],
			'MOV'  => [ 'video/mov', 'video' ],
		];
	}

	public function testGetMediaObjectByUrl() {
		$attachment = self::factory()->attachment->create_object( 'image.jpg' );

		$media = Core\get_media_object_by_url( get_permalink( $attachment ), 'MediaObject' );

		$this->assertInstanceOf( MediaObject::class, $media );
	}

	public function testGetMediaObjectByUrlCanUseAlternateSchema() {
		$attachment = self::factory()->attachment->create_object( 'image.jpg' );

		$media = Core\get_media_object_by_url( get_permalink( $attachment ), 'ImageObject' );

		$this->assertInstanceOf( ImageObject::class, $media );
	}

	public function testGetMediaObjectByUrlReturnsEarlyIfPostDoesNotExist() {
		global $wpdb;

		$this->assertNull(
			$wpdb->get_var( "SELECT ID FROM $wpdb->posts WHERE guid = 'http://example.com/this-does-not-exist'" )
		);

		$this->assertNull( Core\get_media_object_by_url( 'http://example.com/this-does-not-exist' ) );
	}

	public function testGetJson() {
		$post = $this->factory()->post->create_and_get();

		ob_start();
		Core\get_json( $post->ID, 'post' );
		$response = ob_get_clean();

		$this->assertContains( '<script type="application/ld+json">', $response );
		$this->assertContains(
			'"name": "' . $post->post_title . '",',
			$response
		);
	}

	public function testStripNamespace() {
		$this->assertEquals(
			'MyClass',
			Core\strip_namespace( '\A\Whole\Bunch\Of\Namespaces\MyClass' )
		);
	}
}
