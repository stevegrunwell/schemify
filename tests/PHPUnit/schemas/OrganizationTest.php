<?php
/**
 * Tests for the Organization schema.
 *
 * @package Schemify
 */

namespace Schemify\Schemas;

use WP_Mock as M;

use Mockery;
use ReflectionMethod;
use ReflectionProperty;
use Schemify;

class OrganizationTest extends Schemify\TestCase {

	protected $testFiles = array(
		'schemas.php',
	);

	public function testGetLogo() {
		$instance = Mockery::mock( __NAMESPACE__ . '\Organization' )->makePartial();
		$instance->shouldReceive( 'getImage' )
			->once()
			->with( 1 )
			->andReturn( 'http://example.com/image.jpg' );

		$this->assertEquals( 'http://example.com/image.jpg', $instance->getLogo( 1 ) );
	}
}
