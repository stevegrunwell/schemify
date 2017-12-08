<?php
/**
 * Compare our Schemas against the official definition to detect any improper nesting.
 *
 * @package Schemify
 */

namespace Test;

use WP_UnitTestCase;

class SchemaInheritanceTest extends WP_UnitTestCase {

	/**
	 * Log the names of any missing Schema definitions.
	 *
	 * @var array
	 */
	protected $missingSchemas = [];

	/**
	 * Defines how long data should be cached before being updated (90 days).
	 *
	 * The cache can manually be purged by emptying the tests/data/ directory.
	 *
	 * @var int
	 */
	const CACHE_LIFETIME = 7776000;

	/**
	 * Retrieve a data file from local storage or, if the file is either old or missing, retrieve
	 * it from the given URL.
	 *
	 * @param string $filename The filename, relative to the tests/data/ directory.
	 * @param string $url      The URL to retrieve the original file from.
	 *
	 * @return stdClass The decoded JSON from the file.
	 */
	protected static function getData( $filename, $url ) {
		$dir  = dirname( __DIR__ ) . '/data/';
		$file = $dir . $filename;

		// Only download if the cache has expired or the file doesn't exist.
		if ( ! file_exists( $file ) || self::CACHE_LIFETIME < time() - filemtime( $file ) ) {
			mkdir( dirname( $file ) );
			$fh = fopen( $file, 'wb' );
			fwrite( $fh, file_get_contents( $url ) );
			fclose( $fh );
		}

		return json_decode( file_get_contents( $file ), false );
	}

	/**
	 * Verify that a Schema has the appropriate *direct* parent.
	 *
	 * @dataProvider schemaInheritanceProvider
	 * @group        schemaDefinitions
	 */
	public function testSchemaInheritance( $schema, $parent ) {

		if ( ! class_exists( 'Schemify\\Schemas\\' . $schema ) ) {
			$this->missingSchemas[] = $schema;
			$this->markTestSkipped( sprintf( 'The %s Schema is not defined.', $schema ) );
		}

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
	 * @return array
	 */
	public function schemaInheritanceProvider() {
		$definitions = self::getData( 'schema-definitions.json', 'https://schema.org/docs/tree.jsonld' );

		$schemas = array_reduce( $definitions->children, [ $this, 'getDirectParent' ], [
			'Thing' => [ 'Thing', null ],
		] );

		ksort( $schemas );

		return $schemas;
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
