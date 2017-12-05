<?php
/**
 * Tests for the Organization schema.
 *
 * @package Schemify
 */

namespace Test\Schemas;

use Mockery;
use Schemify\Schemas\Organization;
use WP_UnitTestCase;

class OrganizationTest extends WP_UnitTestCase {

	public function testGetLogo() {
		$instance = Mockery::mock( Organization::class )->makePartial();
		$instance->shouldReceive( 'getImage' )
			->andReturn( 'http://example.com/image.jpg' );

		$this->assertEquals( 'http://example.com/image.jpg', $instance->getLogo( 1 ) );
	}
}
