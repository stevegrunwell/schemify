<?php
/**
 * Tests for the Person schema.
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

	public function testGetFamilyName() {
		$user = new \stdClass;
		$user->last_name = 'McTest';

		$instance = Mockery::mock( __NAMESPACE__ . '\Person' )->makePartial();
		$instance->shouldReceive( 'getUser' )->once()->andReturn( $user );

		$this->assertEquals( $user->last_name, $instance->getFamilyName() );
	}

	public function testGetGivenName() {
		$user = new \stdClass;
		$user->first_name = 'Test';

		$instance = Mockery::mock( __NAMESPACE__ . '\Person' )->makePartial();
		$instance->shouldReceive( 'getUser' )->once()->andReturn( $user );

		$this->assertEquals( $user->first_name, $instance->getGivenName() );
	}

	public function testGetImage() {
		$instance = Mockery::mock( __NAMESPACE__ . '\Person' )->makePartial();

		M::wpFunction( 'get_avatar_url', array(
			'times'  => 1,
			'args'   => array( 1, '*' ),
			'return' => 'http://example.com/image.jpg',
		) );

		$this->assertEquals( 'http://example.com/image.jpg', $instance->getImage( 1 ) );
	}

	public function testGetName() {
		$user = new \stdClass;
		$user->display_name = 'Test McTest';

		$instance = Mockery::mock( __NAMESPACE__ . '\Person' )->makePartial();
		$instance->shouldReceive( 'getUser' )->once()->andReturn( $user );

		$this->assertEquals( $user->display_name, $instance->getName( 1 ) );
	}

	public function testGetUrl() {
		$user = new \stdClass;
		$user->user_url = 'http://example.com';

		$instance = Mockery::mock( __NAMESPACE__ . '\Person' )->makePartial();
		$instance->shouldReceive( 'getUser' )->once()->andReturn( $user );

		$this->assertEquals( $user->user_url, $instance->getUrl( 1 ) );
	}
}
