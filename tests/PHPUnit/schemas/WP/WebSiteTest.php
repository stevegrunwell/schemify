<?php
/**
 * Tests for the WebSite WP schema.
 *
 * @package Schemify
 */

namespace Schemify\Schemas\WP;

use WP_Mock as M;

use Mockery;
use ReflectionMethod;
use ReflectionProperty;
use Schemify;

class WebSiteTest extends Schemify\TestCase {

	protected $testFiles = array(
		'schemas.php',
	);

	// Yet another verification of $removeProperties, this time in a WP schema.
	public function testGetRemovedProperties() {
		$instance = new WebSite( 123 );

		$this->assertNull( $instance->getAuthor( 123 ) );
		$this->assertNull( $instance->getPublisher( 123 ) );
		$this->assertNull( $instance->getThumbnailUrl( 123 ) );
	}
}
