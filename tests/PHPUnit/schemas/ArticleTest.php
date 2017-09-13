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

	public function testGetSchemaArray() {
		$instance = Mockery::mock( __NAMESPACE__ . '\Article' )->makePartial();

		M::userFunction( 'Schemify\Core\strip_namespace', array(
			'return_in_order' => array(
				'Article',
				'CreativeWork',
				'Thing',
			),
		) );

		$this->assertEquals( array( 'Article', 'CreativeWork', 'Thing' ), $instance->getSchemaArray() );
	}

	public function testGetPropertiesFiltersResultsWithSchemaNameArray() {
		$data     = array( 'foo' => 'bar' );
		$instance = Mockery::mock( __NAMESPACE__ . '\Article', array( 123, true ) )
			->shouldAllowMockingProtectedMethods()
			->makePartial();
		$instance->shouldReceive( 'getSchema' )->andReturn( 'Article' );
		$instance->shouldReceive( 'getSchemaArray' )->andReturn( array( 'Article', 'CreativeWork', 'Thing' ) );
		$instance->shouldReceive( 'build' )->andReturn( array( 'foo' ) );

		// Filter the data of the parent object CreativeWork, not Article.
		M::onFilter( 'schemify_get_properties_CreativeWork' )
			->with( array( 'foo' ), 'CreativeWork', 123, true )
			->reply( array( 'bar' ) );

		$this->assertEquals( array( 'bar' ), $instance->getProperties() );
	}
}
