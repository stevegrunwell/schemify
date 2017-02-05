<?php
/**
 * Tests for the SearchResultsPage schema.
 *
 * @package Schemify
 */

namespace Schemify\Schemas;

use WP_Mock as M;

use Schemify;

class SearchResultsPageTest extends Schemify\TestCase {

	protected $testFiles = array(
		'schemas.php',
	);

	public function testGetDescription() {
		global $wp_query;

		$instance              = new SearchResultsPage( 123 );
		$wp_query              = new \stdClass;
		$wp_query->found_posts = 67;

		M::wpFunction( 'get_search_query', array(
			'return' => 'SEARCHQUERY',
		) );

		M::wpFunction( 'get_option', array(
			'return' => 'BLOGNAME',
		) );

		M::wpPassthruFunction( '_n' );

		$title = $instance->getDescription( 123 );

		$this->assertContains( '67', $title );
		$this->assertContains( 'SEARCHQUERY', $title );
		$this->assertContains( 'BLOGNAME', $title );

		$wp_query = null;
	}

	public function testGetName() {
		$instance = new SearchResultsPage( 123 );

		M::wpFunction( 'get_search_query', array(
			'return' => 'SEARCHQUERY',
		) );

		M::wpPassthruFunction( '__' );

		$this->assertContains( 'SEARCHQUERY', $instance->getName( 123 ) );
	}
}
