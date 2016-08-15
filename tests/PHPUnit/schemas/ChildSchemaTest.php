<?php
/**
 * Additional tests for how schemas extend one another.
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

class ChildSchemaTest extends Schemify\TestCase {

	protected $testFiles = array(
		'schemas/Thing.php',
	);

	public function testGetPropertyList() {
		require_once ABSPATH . 'TestChildSchema.php';

		$instance          = new TestChildSchema( 123 );
		$parent_reflection = new ReflectionClass( __NAMESPACE__ . '\Thing' );
		$parent_props      = $parent_reflection->getDefaultProperties()['properties'];
		$child_reflection  = new ReflectionClass( $instance );
		$child_props       = $child_reflection->getDefaultProperties()['properties'];
		$method            = new ReflectionMethod( $instance, 'getPropertyList' );
		$method->setAccessible( true );
		$properties        = $method->invoke( $instance );

		foreach ( array( $parent_props, $child_props ) as $set ) {
			foreach ( $set as $prop ) {
				$this->assertContains( $prop, $properties );
			}
		}
	}

	public function testGetPropertyListRemovesValues() {
		require_once ABSPATH . 'TestChildSchema.php';

		$instance          = new TestChildSchema( 123 );
		$parent_reflection = new ReflectionClass( __NAMESPACE__ . '\Thing' );
		$property          = new ReflectionProperty( $instance, 'removeProperties' );
		$property->setAccessible( true );
		$property->setValue( null, array( 'name' ) );
		$method            = new ReflectionMethod( $instance, 'getPropertyList' );
		$method->setAccessible( true );

		// Verify that the parent schema has "name" to begin with.
		$this->assertContains( 'name', $parent_reflection->getDefaultProperties()['properties'] );

		// If this works, 'name' should be removed from the list.
		$this->assertNotContains( 'name', $method->invoke( $instance ) );

		// Important: reset the static TestChildSchema::$removeProperties!
		$property->setValue( null, array() );
	}
}
