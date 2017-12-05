<?php
/**
 * Tests for the Article schema.
 *
 * @package Schemify
 */

namespace Test\Schemas;

use Schemify\Schemas\Article;
use WP_UnitTestCase;

class ArticleTest extends WP_UnitTestCase {

	public function testGetWordCount() {
		$post    = $this->factory()->post->create( [
			'post_content' => 'This post has five words.',
		] );
		$article = new Article( $post );

		$this->assertEquals( 5, $article->getWordCount( $post ) );
	}

	public function testGetWordCountStripsHtml() {
		$post    = $this->factory()->post->create( [
			'post_content' => "<p>This post has five words.</p>\n<p>Just kidding, now it's <strong>ten</strong>.</p>",
		] );
		$article = new Article( $post );

		$this->assertEquals( 10, $article->getWordCount( $post ) );
	}
}
