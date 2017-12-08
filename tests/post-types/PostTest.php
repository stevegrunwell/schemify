<?php
/**
 * Verify the output of posts.
 *
 * @package Schemify
 */

namespace Test\PostTypes;

use Schemify\Core as Core;

/**
 * @group postTypes
 */
class PostTest extends TestCase {

	/**
	 * A generic post object.
	 */
	public function testBasicPostElements() {
		$post = $this->factory()->post->create_and_get([
			'post_excerpt' => 'Post excerpt',
			'post_content' => 'Post content',
		]);
		$schema = Core\build_object( $post->ID, 'post' );

		$this->assertEquals( 'BlogPosting', $schema['@type'] );
		$this->assertNotEmpty( $schema['dateCreated'] );
		$this->assertNotEmpty( $schema['dateModified'] );
		$this->assertNotEmpty( $schema['datePublished'] );
		$this->assertEquals( $post->post_excerpt, $schema['description'] );
		$this->assertEquals( $post->post_title, $schema['headline'] );
		$this->assertEquals( $post->post_title, $schema['name'] );
		$this->assertEquals( get_permalink( $post->ID ), $schema['url'] );
		$this->assertEquals( 2, $schema['wordCount'] );

		$this->assertPerson( $schema['author'], $post->post_author );
		$this->assertOrganization( $schema['publisher'] );
	}
}
