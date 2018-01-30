<?php
/**
 * Tests for the WebSite schema.
 *
 * @package Schemify
 */

namespace Test\Schemas;

use Schemify\Schemas\WebSite;
use WP_UnitTestCase;

class WebSiteTest extends WP_UnitTestCase {

	public function testGetAuthorAlwaysReturnsNull() {
		$website = new WebSite( 123, true );

		$this->assertNull( $website->getAuthor( 123 ) );
	}
}
