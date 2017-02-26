<?php
/**
 * Tests for the WP_Schema trait.
 *
 * @package Schemify
 */

namespace Schemify\Schemas\WP;

use WP_Mock as M;

use Mockery;
use ReflectionMethod;
use ReflectionProperty;
use Schemify;

class WPSchemaTest extends Schemify\TestCase {

	protected $testFiles = array(
		'schemas.php',
	);

	public function testGetSchema() {
		require_once ABSPATH . 'TestSchemaTrait.php';

		$instance = new TestSchemaTrait( 123 );
		$property = new ReflectionProperty( $instance, 'schema' );
		$property->setAccessible( true );
		$unique   = uniqid();

		M::userFunction( 'Schemify\Core\strip_namespace', array(
			'times'  => 1,
			'return' => $unique,
		) );

		$this->assertEquals( $unique, $instance->getSchema( 123 ) );
		$this->assertEquals( $unique, $property->getValue( $instance ) );
	}

	public function testGetSchemaCachesResult() {
		require_once ABSPATH . 'TestSchemaTrait.php';

		$instance = new TestSchemaTrait( 123 );
		$property = new ReflectionProperty( $instance, 'schema' );
		$property->setAccessible( true );
		$unique   = uniqid();

		M::userFunction( 'Schemify\Core\strip_namespace', array(
			'return' => $unique,
		) );

		$this->assertNull( $property->getValue( $instance ) );
		$this->assertEquals( $unique, $instance->getSchema( 123 ) );
		$this->assertEquals( $unique, $property->getValue( $instance ) );
	}

	public function testGetSchemaReadsFromCache() {
		require_once ABSPATH . 'TestSchemaTrait.php';

		$instance = new TestSchemaTrait( 123 );
		$property = new ReflectionProperty( $instance, 'schema' );
		$property->setAccessible( true );
		$property->setValue( $instance, 'TestSchema' );

		M::userFunction( 'Schemify\Core\strip_namespace', array(
			'times' => 0,
		) );

		$this->assertEquals( 'TestSchema', $instance->getSchema( 123 ) );
	}
}
