<?php
/**
 * Tests for the CreativeWork schema.
 *
 * @package Schemify
 */

namespace Schemify\Schemas;

use WP_Mock as M;

use Mockery;
use ReflectionMethod;
use ReflectionProperty;
use Schemify;

class ArticleTest extends Schemify\TestCase {

	protected $testFiles = array(
		'schemas.php',
	);

	public function testGetWordCount() {
		$instance = new Article( 123 );
		$content  = 'This post has five words.';

		M::userFunction( 'get_post_field', array(
			'times'  => 1,
			'args'   => array( 'post_content', 123 ),
			'return' => $content,
		) );

		$this->assertEquals( 5, $instance->getWordCount( 123 ) );
	}

	public function testGetWordCountIgnoresHtml() {
		$instance = new Article( 123 );
		$content  = <<<EOT
<p>This post has five words.</p>
<p>Just kidding, now it's <strong>ten</strong>.</p>
EOT;

		M::userFunction( 'get_post_field', array(
			'return' => $content,
		) );

		$this->assertEquals( 10, $instance->getWordCount( 123 ) );
	}
}
