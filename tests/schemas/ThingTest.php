<?php
/**
 * Tests for the basic Thing schema.
 *
 * @package Schemify
 */

namespace Test\Schemas;

use JsonSerializable;
use Mockery;
use ReflectionMethod;
use ReflectionProperty;
use Schemify\Schemas\ImageObject;
use Schemify\Schemas\Thing;
use WP_UnitTestCase;

class ThingTest extends WP_UnitTestCase {

	public function testSchemaCanBeSerialized() {
		$thing = new Thing( 1 );

		$this->assertInstanceOf(
			JsonSerializable::class,
			$thing,
			'Schemas should implement JsonSerializable.'
		);
		$this->assertTrue( method_exists( $thing, 'jsonSerialize' ) );
	}

	public function testGetProperties() {
		$post  = $this->factory()->post->create_and_get();
		$thing = new Thing( $post->ID );

		$props = $thing->getProperties();

		$this->assertInternalType( 'array', $props );
		$this->assertEquals( 'Thing', $props['@type'] );
		$this->assertEquals( $post->post_title, $props['name'] );
		$this->assertArrayHasKey( 'description', $props );
		$this->assertArrayHasKey( 'url', $props );
	}

	public function testGetPropMethod() {
		$post  = $this->factory()->post->create_and_get();
		$thing = Mockery::spy( Thing::class, [ $post->ID ] )->makePartial();

		$this->assertEquals( $post->post_title, $thing->getProp( 'name' ) );
		$thing->shouldHaveReceived( 'getName' )->once()->with( $post->ID );
	}

	public function testJsonSerialize() {
		$thing = new Thing( $this->factory()->post->create() );

		$this->assertInternalType( 'array', $thing->jsonSerialize() );
	}

	public function testGetSchema() {
		$thing = new Thing( 1 );

		$this->assertEquals( 'Thing', $thing->getSchema() );
	}

	public function testGetSchemaReadsFromCache() {
		$thing = new Thing( 1 );
		$prop  = new ReflectionProperty( $thing, 'schema' );
		$prop->setAccessible( true );
		$prop->setValue( $thing, 'SomeCustomSchema' );

		$this->assertEquals( 'SomeCustomSchema', $thing->getSchema() );
	}

	public function testBuild() {
		$this->markTestIncomplete();
	}

	public function testGetPropertyList() {
		$this->markTestIncomplete( 'This test may be better served in a deeper schema.' );
	}

	public function testGetPropertyListReadsFromCache() {
		$thing = new Thing( 1 );
		$prop  = new ReflectionProperty( $thing, 'propertyList' );
		$prop->setAccessible( true );
		$prop->setValue( $thing, [ 'foo', 'bar', 'baz' ] );
		$method = new ReflectionMethod( $thing, 'getPropertyList' );
		$method->setAccessible( true );

		$this->assertEquals(
			[ 'foo', 'bar', 'baz' ],
			$method->invoke( $thing )
		);
	}

	public function testGetDescription() {
		$post  = $this->factory()->post->create( [
			'post_excerpt' => 'This is an excerpt',
		] );
		$thing = new Thing( $post );

		$this->assertEquals( 'This is an excerpt', $thing->getDescription( $post ) );
	}

	public function testGetName() {
		$post  = $this->factory()->post->create_and_get();
		$thing = new Thing( $post->ID );

		$this->assertEquals( $post->post_title, $thing->getName( $post->ID ) );
	}

	public function testGetUrl() {
		$post  = $this->factory()->post->create();
		$thing = new Thing( $post );

		$this->assertEquals( get_permalink( $post ), $thing->getUrl( $post ) );
	}

	public function testGetImage() {
		$post  = $this->factory()->post->create();
		$image = $this->factory()->attachment->create_object([
			'file'           => 'test.jpg',
			'post_mime_type' => 'image/jpeg',
		]);
		$thing = new Thing( $post, true );

		set_post_thumbnail( $post, $image );

		$this->assertInstanceOf( ImageObject::class, $thing->getImage( $post ) );
	}

	public function testGetImageReturnsNullWhenThingIsNotMain() {
		$post  = $this->factory()->post->create();
		$image = $this->factory()->attachment->create_object([
			'file'           => 'test.jpg',
			'post_mime_type' => 'image/jpeg',
		]);
		$thing = new Thing( $post, false );

		set_post_thumbnail( $post, $image );

		$this->assertNull( $thing->getProp( 'image' ) );
	}

	public function testGetImageReturnsNullIfNoFeaturedImage() {
		$post  = $this->factory()->post->create();
		$thing = new Thing( $post, true );

		$this->assertNull( $thing->getImage( $post ) );
	}
}
