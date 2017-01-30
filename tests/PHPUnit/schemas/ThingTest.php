<?php
/**
 * Tests for the Thing schema.
 *
 * @package Schemify
 */

namespace Schemify\Schemas;

use WP_Mock as M;

use Mockery;
use ReflectionClass;
use ReflectionMethod;
use ReflectionProperty;
use Schemify;

class ThingTest extends Schemify\TestCase {

	protected $testFiles = array(
		'schemas/Thing.php',
	);

	public function test__construct() {
		$instance = Mockery::mock( __NAMESPACE__ . '\Thing' )->makePartial();
		$post_id  = new ReflectionProperty( $instance, 'postId' );
		$post_id->setAccessible( true );
		$is_main  = new ReflectionProperty( $instance, 'isMain' );
		$is_main->setAccessible( true );

		// Before we've done anything.
		$this->assertEmpty( $post_id->getValue( $instance ) );
		$this->assertEmpty( $is_main->getValue( $instance ) );

		// Construct + test.
		$instance = new Thing( 123, true );

		$this->assertEquals( 123, $post_id->getValue( $instance ) );
		$this->assertTrue( $is_main->getValue( $instance ) );
	}

	public function testGetProperties() {
		$data     = array( 'foo' => 'bar' );
		$instance = Mockery::mock( __NAMESPACE__ . '\Thing' )
			->shouldAllowMockingProtectedMethods()
			->makePartial();
		$instance->shouldReceive( 'getSchema' )
			->once()
			->andReturn( 'Thing' );
		$instance->shouldReceive( 'build' )
			->once()
			->andReturn( $data );

		$this->assertEquals( $data, $instance->getProperties() );
	}

	public function testGetPropertiesFiltersResultsWithSchemaName() {
		$data     = array( 'foo' => 'bar' );
		$instance = Mockery::mock( __NAMESPACE__ . '\Thing', array( 123, true ) )
			->shouldAllowMockingProtectedMethods()
			->makePartial();
		$instance->shouldReceive( 'getSchema' )->andReturn( 'Thing' );
		$instance->shouldReceive( 'build' )->andReturn( array( 'foo' ) );

		M::onFilter( 'schemify_get_properties_Thing' )
			->with( array( 'foo' ), 'Thing', 123, true )
			->reply( array( 'bar' ) );

		$this->assertEquals( array( 'bar' ), $instance->getProperties() );
	}

	public function testGetPropertiesFiltersResults() {
		$data     = array( 'foo' => 'bar' );
		$instance = Mockery::mock( __NAMESPACE__ . '\Thing', array( 123, true ) )
			->shouldAllowMockingProtectedMethods()
			->makePartial();
		$instance->shouldReceive( 'getSchema' )->andReturn( 'Thing' );
		$instance->shouldReceive( 'build' )->andReturn( array( 'foo' ) );

		M::onFilter( 'schemify_get_properties' )
			->with( array( 'foo' ), 'Thing', 123, true )
			->reply( array( 'bar' ) );

		$this->assertEquals( array( 'bar' ), $instance->getProperties() );
	}

	public function testGetPropertiesCachesResults() {
		$data     = array( 'foo' => 'bar' );
		$instance = Mockery::mock( __NAMESPACE__ . '\Thing' )
			->shouldAllowMockingProtectedMethods()
			->makePartial();
		$instance->shouldReceive( 'build' )
			->once()
			->andReturn( $data );
		$property = new ReflectionProperty( $instance, 'data' );
		$property->setAccessible( true );

		// Ensure we're starting empty
		$this->assertEmpty( $property->getValue( $instance ) );

		// Execute, verify our $data is now in $this->data.
		$instance->getProperties();

		$this->assertEquals( $data, $property->getValue( $instance ) );
	}

	public function testGetPropertiesCastsOutputAsArray() {
		$data = new \stdClass;
		$data->foo = 'bar';

		$instance = Mockery::mock( __NAMESPACE__ . '\Thing' )->makePartial();
		$property = new ReflectionProperty( $instance, 'data' );
		$property->setAccessible( true );
		$property->setValue( $instance, $data );

		$this->assertEquals( array( 'foo' => 'bar' ), $instance->getProperties() );
	}

	public function testGetProp() {
		$instance = Mockery::mock( __NAMESPACE__ . '\Thing', array( 123, true ) )->makePartial();
		$instance->shouldReceive( 'getName' )
			->once()
			->with( 123 )
			->andReturn( 'Object Name' );

		// Make sure our test assumptions are accurate.
		$this->assertTrue( method_exists( $instance, 'getName' ), 'Invalid test, as getName is not a valid property callback' );

		$this->assertEquals( 'Object Name', $instance->getProp( 'name' ) );
	}

	public function testJsonSerialize() {
		$uniqid = uniqid();

		$instance = Mockery::mock( __NAMESPACE__ . '\Thing' )->makePartial();
		$instance->shouldReceive( 'getProperties' )
			->once()
			->andReturn( $uniqid );

		$this->assertEquals( $uniqid, $instance->jsonSerialize() );
	}

	public function testGetSchema() {
		$instance = Mockery::mock( __NAMESPACE__ . '\Thing' )->makePartial();

		M::wpFunction( 'Schemify\Core\strip_namespace', array(
			'times'  => 1,
			'return' => 'Thing',
		) );

		$this->assertEquals( 'Thing', $instance->getSchema() );
	}

	public function testGetSchemaCachesResult() {
		$instance = Mockery::mock( __NAMESPACE__ . '\Thing' )->makePartial();
		$property = new ReflectionProperty( $instance, 'schema' );
		$property->setAccessible( true );

		M::wpFunction( 'Schemify\Core\strip_namespace', array(
			'return' => 'Thing',
		) );

		$instance->getSchema();

		$this->assertEquals( 'Thing', $property->getValue( $instance ) );
	}

	public function testGetSchemaPullsFromCache() {
		$instance = Mockery::mock( __NAMESPACE__ . '\Thing' )->makePartial();
		$random   = uniqid();
		$property = new ReflectionProperty( $instance, 'schema' );
		$property->setAccessible( true );
		$property->setValue( $instance, $random );


		$this->assertEquals( $random, $instance->getSchema() );
	}

	public function testBuild() {
		$instance = Mockery::mock( __NAMESPACE__ . '\Thing' )
			->shouldAllowMockingProtectedMethods()
			->makePartial();
		$instance->shouldReceive( 'getPropertyList' )
			->once()
			->andReturn( array( 'someProp' ) );
		$instance->shouldReceive( 'getSchema' )
			->once()
			->andReturn( 'Thing' );
		$instance->shouldReceive( 'getProp' )
			->once()
			->with( 'someProp' )
			->andReturn( 'value' );
		$method   = new ReflectionMethod( $instance, 'build' );
		$method->setAccessible( true );
		$value    = array(
			'@context' => 'http://schema.org',
			'@type'    => 'Thing',
			'someProp' => 'value',
		);

		M::wpFunction( 'wp_cache_get', array(
			'times'  => 1,
			'args'   => array( 'schema_123', 'schemify', false ),
			'return' => false,
		) );

		M::wpFunction( 'wp_cache_set', array(
			'times'  => 1,
			'args'   => array( 'schema_123', $value, 'schemify', 0 ),
		) );

		$this->assertEquals( $value, $method->invoke( $instance, 123, true ) );
	}

	public function testBuildReturnsFromCache() {
		$instance = new Thing( 123, true );
		$method   = new ReflectionMethod( $instance, 'build' );
		$method->setAccessible( true );

		M::wpFunction( 'wp_cache_get', array(
			'return' => array( 'schema', 'data' ),
		) );

		$this->assertEquals( array( 'schema', 'data' ), $method->invoke( $instance, 123, true ) );
	}

	public function testBuildOnlyReturnsFromCacheForTheMainSchema() {
		$instance = Mockery::mock( __NAMESPACE__ . '\Thing' )
			->shouldAllowMockingProtectedMethods()
			->makePartial();
		$instance->shouldReceive( 'getPropertyList' )->andReturn( array() );
		$instance->shouldReceive( 'getSchema' )->andReturn( 'Thing' );
		$method   = new ReflectionMethod( $instance, 'build' );
		$method->setAccessible( true );

		M::wpFunction( 'wp_cache_get', array(
			'return' => array( 'schema', 'data' ),
		) );

		$this->assertNotEquals( array( 'schema', 'data' ), $method->invoke( $instance, 123, false ) );
	}

	/**
	 * Verifies that ID's aren't being forced into integers, as that could cause issues with caching.
	 */
	public function testBuildWithHomepage() {
		$instance = new Thing( 'home', true );
		$method   = new ReflectionMethod( $instance, 'build' );
		$method->setAccessible( true );

		M::wpFunction( 'wp_cache_get', array(
			'times'  => 1,
			'args'   => array( 'schema_home', 'schemify', false ),
			'return' => array( 'schema', 'data' ),
		) );

		$method->invoke( $instance, 'home', true );
	}

	// Since most of this logic requires a descendant class, @see ChildSchemaTest.
	public function testGetPropertyList() {
		$instance = new Thing( 123, true );
		$method   = new ReflectionMethod( $instance, 'getPropertyList' );
		$method->setAccessible( true );
		$property = new ReflectionClass( $instance );

		$this->assertEquals( $property->getDefaultProperties()['properties'], $method->invoke( $instance ) );
	}

	public function testGetPropertyListCachesValue() {
		$uniqid   = (array) uniqid();
		$instance = new Thing( 123, true );
		$method   = new ReflectionMethod( $instance, 'getPropertyList' );
		$method->setAccessible( true );
		$property = new ReflectionProperty( $instance, 'propertyList' );
		$property->setAccessible( true );

		// Before running the method.
		$this->assertEmpty( $property->getValue( $instance ) );

		// After running.
		$method->invoke( $instance );
		$this->assertNotEmpty( $property->getValue( $instance ) );
	}

	public function testGetPropertyListReadsFromCache() {
		$uniqid   = (array) uniqid();
		$instance = new Thing( 123, true );
		$method   = new ReflectionMethod( $instance, 'getPropertyList' );
		$method->setAccessible( true );
		$property = new ReflectionProperty( $instance, 'propertyList' );
		$property->setAccessible( true );
		$property->setValue( $instance, $uniqid );

		$this->assertEquals( $uniqid, $method->invoke( $instance ) );
	}

	public function testMergeSchemaProperties() {
		$instance = new Thing( 123, true );
		$method   = new ReflectionMethod( $instance, 'mergeSchemaProperties' );
		$method->setAccessible( true );

		$this->assertEquals(
			array( 'foo', 'bar', 'baz' ),
			$method->invoke( $instance, array( 'foo', 'bar' ), array( 'baz' ) )
		);
	}

	public function testGetDescription() {
		$instance = new Thing( 123, true );

		M::wpFunction( 'get_the_excerpt', array(
			'times'  => 1,
			'args'   => array( 123 ),
			'return' => 'Excerpt',
		) );

		M::wpPassthruFunction( 'esc_html' );

		$this->assertEquals( 'Excerpt', $instance->getDescription( 123 ) );
	}

	public function testGetName() {
		$instance = new Thing( 123, true );

		M::wpFunction( 'get_the_title', array(
			'times'  => 1,
			'args'   => array( 123 ),
			'return' => 'Name',
		) );

		$this->assertEquals( 'Name', $instance->getName( 123 ) );
	}

	public function testGetImage() {
		$instance = new Thing( 123, true );

		M::wpFunction( 'get_post_thumbnail_id', array(
			'times'  => 1,
			'args'   => array( 123 ),
			'return' => 124,
		) );

		$this->assertInstanceOf( __NAMESPACE__ . '\ImageObject', $instance->getImage( 123 ) );
	}

	public function testGetImageIsNullWhenNotMainSchema() {
		$instance = new Thing( 123, false );

		M::wpFunction( 'get_post_thumbnail_id', array(
			'times'  => 0,
		) );

		$this->assertNull( $instance->getImage( 123 ) );
	}

	public function testGetUrl() {
		$instance = new Thing( 123, true );

		M::wpFunction( 'get_permalink', array(
			'times'  => 1,
			'args'   => array( 123 ),
			'return' => 'http://example.com',
		) );

		$this->assertEquals( 'http://example.com', $instance->getUrl( 123 ) );
	}
}
