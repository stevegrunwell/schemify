<?php
/**
 * Tests for the WebSite schema.
 *
 * @package Schemify
 */

namespace Schemify\Schemas;

use Schemify;

class WebSiteTest extends Schemify\TestCase {

	protected $testFiles = array(
		'schemas.php',
	);

	public function testGetAuthorCallsGetPublisher() {
		$instance = new WebSite( 123, true );

		$this->assertNull( $instance->getAuthor( 123 ) );
	}
}
