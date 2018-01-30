<?php
/**
 * Compare our Schemas against the official definition to detect any improper nesting.
 *
 * @package Schemify
 */

namespace Test\Schemas;

use ReflectionMethod;
use WP_UnitTestCase;

/**
 * Test Schemify's Schema definitions against the official specification.
 *
 * @group schemaDefinitions
 */
class SchemaInheritanceTest extends WP_UnitTestCase {

	/**
	 * Log the names of any missing Schema definitions.
	 *
	 * @var array
	 */
	protected static $missingSchemas = [];

	/**
	 * Defines how long data should be cached before being updated (90 days).
	 *
	 * The cache can manually be purged by emptying the tests/data/ directory.
	 *
	 * @var int
	 */
	const CACHE_LIFETIME = 7776000;

	/**
	 * Verify that a Schema has the appropriate *direct* parent.
	 *
	 * @dataProvider schemaInheritanceProvider
	 *
	 * @param string      $schema The schema name.
	 * @param string|null $parent The schema's direct parent name, or NULL in the case of "Thing".
	 */
	public function testSchemaInheritance( $schema, $parent ) {

		// Verify that the Schema exists first, otherwise skip the test.
		if ( ! class_exists( 'Schemify\\Schemas\\' . $schema ) ) {
			self::$missingSchemas[] = $schema;
			$this->markTestSkipped( sprintf( 'The %s Schema is not defined.', $schema ) );
		}

		// Ensure the Schema's direct parent matches $parent.
		if ( null !== $parent ) {
			$this->assertEquals(
				'Schemify\\Schemas\\' . $parent,
				get_parent_class( '\\Schemify\\Schemas\\' . $schema ),
				sprintf( 'According to the specification, %1$s should extend %2$s.', $schema, $parent )
			);
		}
	}

	/**
	 * Compile an array of all known Schemas, along with their direct parent.
	 *
	 * Each entry in the array will have the format:
	 *
	 *     $schema => [ $schema, $parent ]
	 *
	 * @return array
	 */
	public function schemaInheritanceProvider() {
		$definitions = $this->getData( 'schema-definitions.json', 'https://schema.org/docs/tree.jsonld' );

		$schemas = array_reduce( $definitions->children, [ $this, 'getDirectParent' ], [
			'Thing' => [ 'Thing', null ],
		] );

		ksort( $schemas );

		return $schemas;
	}

	/**
	 * Iterate through each Schema and verify that all properties are accounted for.
	 *
	 * @dataProvider definedSchemaNameProvider()
	 *
	 * @param string $schema The schema name.
	 */
	public function testSchemaProperties( $schema ) {
		$definition = $this->getData( $schema . '.json', 'https://schema.org/' . $schema . '.jsonld' );

		// Return early if there's no @graph definition.
		if ( ! isset( $definition->{'@graph'} ) ) {
			$this->markTestSkipped( sprintf( 'There are no properties defined for the %s schema.', $schema ) );
		}

		// Iterate through the properties defined in the Schema definition.
		$props = array_reduce( $definition->{'@graph'}, function ( $props, $entry ) {
			if (
				isset( $entry->{'@type'}, $entry->{'rdfs:label'} )
				&& 'rdf:Property' === $entry->{'@type'}
				&& empty( $entry->{'schema:isPartOf'} )
				&& empty( $entry->{'schema:supersededBy'} )
			) {
				$props[] = is_object( $entry->{'rdfs:label'} ) ? $entry->{'rdfs:label'}->{'@value'} : $entry->{'rdfs:label'};
			}

			return $props;
		}, [] );

		// Construct an instance of the Schema to work against.
		$class    = 'Schemify\\Schemas\\' . $schema;
		$instance = new $class( 1, true );
		$propList = new ReflectionMethod( $instance, 'getPropertyList' );
		$propList->setAccessible( true );

		// Compare the specification against what the Schema class is reporting.
		$difference = array_diff( $props, $propList->invoke( $instance ) );
		sort( $difference );

		$this->assertEmpty(
			$difference,
			"Property list does not match specification:\n - " . implode( "\n - ", $difference )
		);
	}

	/**
	 * Data provider that retrieves an array of *only* defined Schemas (by name).
	 *
	 * @return array
	 */
	public function definedSchemaNameProvider() {
		$schemas = $this->schemaInheritanceProvider();

		// Filter out any Schemas that are undefined via code.
		return array_filter( $schemas, function ( $schema ) {
			return 'Thing' !== $schema && class_exists( 'Schemify\\Schemas\\' . $schema );
		}, ARRAY_FILTER_USE_KEY );
	}

	/**
	 * Write a report of missing schemas at the end of the run.
	 *
	 * @afterClass
	 */
	public static function writeMissingSchemaReport() {
		$file = dirname( __DIR__ ) . '/coverage/missing-schemas.txt';

		@mkdir( dirname( $file ) );
		$fh = fopen( $file, 'wb' );
		fwrite( $fh, implode( PHP_EOL, self::$missingSchemas ) );
		fclose( $fh );

		echo esc_html( PHP_EOL . PHP_EOL . 'Missing Schema names have been written to ' . $file );
	}

	/**
	 * Retrieve a data file from local storage or, if the file is either old or missing, retrieve
	 * it from the given URL.
	 *
	 * @param string $filename The filename, relative to the tests/data/ directory.
	 * @param string $url      The URL to retrieve the original file from.
	 *
	 * @return stdClass The decoded JSON from the file.
	 */
	protected function getData( $filename, $url ) {
		$dir  = dirname( __DIR__ ) . '/data/';
		$file = $dir . $filename;

		// Only download if the cache has expired or the file doesn't exist.
		if ( ! file_exists( $file ) || self::CACHE_LIFETIME < time() - filemtime( $file ) ) {
			@mkdir( dirname( $file ) );
			$fh = fopen( $file, 'wb' );
			fwrite( $fh, file_get_contents( $url ) );
			fclose( $fh );
		}

		return json_decode( file_get_contents( $file ), false );
	}

	/**
	 * Build a collection of all known Schemas, with only their names and direct parents.
	 *
	 * This method is designed to be run recursively, building out a list of all schemas regardless
	 * of depth in the tree.
	 *
	 * @param array    $schemas The array of all schemas.
	 * @param stdClass $schema  The current schema being processed.
	 *
	 * @return array The updated $schemas array.
	 */
	protected function getDirectParent( $schemas, $schema ) {

		// Bail out if the Schema exists in a layer we're not dealing with.
		if ( 'core' !== $schema->layer ) {
			return $schemas;
		}

		$schemas[ $schema->name ] = [
			$schema->name,
			str_replace( 'schema:', '', $schema->{'rdfs:subClassOf'} ),
		];

		if ( ! empty( $schema->children ) ) {
			$schemas = array_reduce( $schema->children, [ $this, 'getDirectParent' ], $schemas );
		}

		return $schemas;
	}
}
