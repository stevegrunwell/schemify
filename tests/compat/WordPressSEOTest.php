<?php
/**
 * Base test for compatibility files.
 *
 * @package Schemify
 */

namespace Test\Compat;

use Schemify\Core as Core;

/**
 * @group compatibility
 */
class WordPressSEOTest extends BaseTest {

	protected static $compatFile = 'wordpress-seo/wp-seo.php';

	/**
	 * If a user has populated the extra social fields from Yoast SEO, include them.
	 */
	public function testAddUserProfileUrls() {
		$user = $this->factory()->user->create();

		add_user_meta( $user, 'googleplus', 'https://plus.google.com/+JohnDoe' );
		add_user_meta( $user, 'facebook', 'https://facebook.com/johndoe' );
		add_user_meta( $user, 'twitter', 'johndoe' );

		$schema = Core\build_object( $user, 'user' );

		$this->assertContains( 'https://plus.google.com/+JohnDoe', $schema['sameAs'] );
		$this->assertContains( 'https://facebook.com/johndoe', $schema['sameAs'] );
		$this->assertContains( 'https://twitter.com/johndoe', $schema['sameAs'] );
	}

	/**
	 * If a post is created without an image but a default has been set in Yoast SEO, use it.
	 */
	public function testSetDefaultImage() {
		$post       = $this->factory()->post->create();
		$attachment = $this->factory()->attachment->create_object( [
			'file'           => 'default.png',
			'post_mime_type' => 'image/png',
		] );
		update_option( 'wpseo_social', [
			'og_default_image' => get_permalink( $attachment ),
		] );

		$schema = Core\build_object( $post, 'post' );

		$this->assertEquals( 'default.png', basename( $schema['thumbnailUrl'] ) );
	}

	/**
	 * If an image has been set on a particular object, don't overwrite it with the default.
	 */
	public function testSetDefaultImageDoesNotOverrideExistingImage() {
		$filename   = uniqid() . '.jpg';
		$post       = $this->factory()->post->create();
		$attachment = $this->factory()->attachment->create_object( [
			'file'           => $filename,
			'post_mime_type' => 'image/jpeg',
		] );
		set_post_thumbnail( $post, $attachment );

		update_option( 'wpseo_social', [
			'og_default_image' => 'http://example.com/this-was-the-default.jpg',
		] );

		$schema = Core\build_object( $post, 'post' );

		$this->assertEquals( $filename, basename( $schema['thumbnailUrl'] ) );
	}

	/**
	 * Inject the Yoast SEO home/front page image, if set.
	 */
	public function testSetDefaultImageOnFrontPage() {
		$this->markTestSkipped( 'Test works on its own, but fails when run as part of a suite.' );

		$post = $this->factory()->post->create( [
			'post_type' => 'page',
		] );
		update_option( 'show_on_front', 'page' );
		update_option( 'page_on_front', $post );

		$attachment = $this->factory()->attachment->create_object( [
			'file'           => 'default-front.png',
			'post_mime_type' => 'image/png',
		] );
		update_option( 'wpseo_social', [
			'og_default_image'   => 'http://example.com/this-was-the-default.jpg',
			'og_frontpage_image' => get_permalink( $attachment ),
		] );

		$this->go_to( '/' );

		$schema = Core\build_object( $post, 'post' );

		$this->assertEquals( 'default-front.png', basename( $schema['image']->getProp( 'contentUrl' ) ) );
	}
}
