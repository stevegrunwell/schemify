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

class CreativeWorkTest extends Schemify\TestCase {

	protected $testFiles = array(
		'schemas.php',
	);

	public function testGetAuthor() {
		$instance = new CreativeWork( 123, true );
		$post     = new \stdClass;
		$post->post_author = 5;

		M::userFunction( 'get_post', array(
			'times'  => 1,
			'return' => $post,
		) );

		$this->assertInstanceOf( __NAMESPACE__ . '\Person', $instance->getAuthor( 5 ) );
	}

	public function testGetAuthorReturnsNullIfNotMain() {
		$instance = new CreativeWork( 123, false );

		M::userFunction( 'get_post', array(
			'times'  => 0,
		) );

		$this->assertNull( $instance->getAuthor( 5 ) );
	}

	public function testGetAuthorReturnsNullIfPostNotFound() {
		$instance = new CreativeWork( 123, false );

		M::userFunction( 'get_post', array(
			'return' => null,
		) );

		$this->assertNull( $instance->getAuthor( 5 ) );
	}

	public function testGetDateCreated() {
		$instance = new CreativeWork( 123 );

		M::userFunction( 'get_post_time', array(
			'times'  => 1,
			'args'   => array( 'c', true, 123 ),
			'return' => 'Time',
		) );

		$this->assertEquals( 'Time', $instance->getDateCreated( 123 ) );
	}

	public function testGetDateModified() {
		$instance = new CreativeWork( 123 );

		M::userFunction( 'get_post_modified_time', array(
			'times'  => 1,
			'args'   => array( 'c', true, 123 ),
			'return' => 'Time',
		) );

		$this->assertEquals( 'Time', $instance->getDateModified( 123 ) );
	}

	public function testGetDatePublished() {
		$instance = Mockery::mock( __NAMESPACE__ . '\CreativeWork' )->makePartial();
		$instance->shouldReceive( 'getDateCreated' )
			->once()
			->with( 123 )
			->andReturn( 'Time' );

		$this->assertEquals( 'Time', $instance->getDatePublished( 123 ) );
	}

	public function testGetHeadline() {
		$instance = Mockery::mock( __NAMESPACE__ . '\CreativeWork' )->makePartial();
		$instance->shouldReceive( 'getName' )
			->once()
			->with( 123 )
			->andReturn( 'Name' );

		$this->assertEquals( 'Name', $instance->getHeadline( 123 ) );
	}

	public function testGetPublisher() {
		$instance = new CreativeWork( 123, true );

		M::userFunction( 'get_current_blog_id', array(
			'times'  => 1,
			'return' => 1,
		) );

		$this->assertInstanceOf( __NAMESPACE__ . '\Organization', $instance->getPublisher( 1 ) );
	}

	public function testGetPublisherIsNullWhenNotMainSchema() {
		$instance = new CreativeWork( 123, false );

		M::userFunction( 'get_current_blog_id', array(
			'times'  => 0,
		) );

		$this->assertNull( $instance->getPublisher( 1 ) );
	}

	public function testGetThumbnailUrl() {
		$instance = new CreativeWork( 123 );

		M::userFunction( 'get_post_thumbnail_id', array(
			'times'  => 1,
			'args'   => array( 123 ),
			'return' => 777,
		) );

		M::userFunction( 'wp_get_attachment_image_src', array(
			'times'  => 1,
			'args'   => array( 777, 'thumbnail', true ),
			'return' => array( 'URL', 150, 150 ),
		) );

		$this->assertEquals( 'URL', $instance->getThumbnailUrl( 123 ) );
	}
}
