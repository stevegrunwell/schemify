<?php
/**
 * Tests for the Person schema.
 *
 * @package Schemify
 */

namespace Schemify\Schemas\WP;

use WP_Mock as M;

use Mockery;
use ReflectionMethod;
use ReflectionProperty;
use Schemify;

class UserTest extends Schemify\TestCase {

	protected $testFiles = array(
		'schemas.php',
	);

	public function testGetUser() {
		$instance = new User( 1 );
		$user     = Mockery::mock( 'WP_User' );

		M::userFunction( 'get_user_by', array(
			'times'  => 1,
			'args'   => array( 'id', 1 ),
			'return' => $user,
		) );

		$this->assertInstanceOf( 'WP_User', $instance->getUser() );
	}

	public function testGetUserCachesResult() {
		$instance = new User( 1 );
		$property = new ReflectionProperty( $instance, 'user' );
		$property->setAccessible( true );
		$user     = uniqid();

		M::userFunction( 'get_user_by', array(
			'times'  => 1,
			'args'   => array( 'id', 1 ),
			'return' => $user,
		) );

		$this->assertNull( $property->getValue( $instance ) );

		$instance->getUser();

		$this->assertEquals( $user, $property->getValue( $instance ) );
	}

	public function testGetUserPullsFromCache() {
		$instance = new User( 1 );
		$property = new ReflectionProperty( $instance, 'user' );
		$property->setAccessible( true );
		$user     = uniqid();

		M::userFunction( 'get_user_by', array(
			'times'  => 0,
		) );

		$property->setValue( $instance, $user );

		$this->assertEquals( $user, $instance->getUser() );
	}

	public function testGetDescription() {
		$user = new \stdClass;
		$user->description = 'Bacon ipsum';

		$instance = Mockery::mock( __NAMESPACE__ . '\User' )->makePartial();
		$instance->shouldReceive( 'getUser' )->once()->andReturn( $user );

		$this->assertEquals( $user->description, $instance->getDescription( 1 ) );
	}

	public function testGetFamilyName() {
		$user = new \stdClass;
		$user->last_name = 'McTest';

		$instance = Mockery::mock( __NAMESPACE__ . '\User' )->makePartial();
		$instance->shouldReceive( 'getUser' )->once()->andReturn( $user );

		$this->assertEquals( $user->last_name, $instance->getFamilyName() );
	}

	public function testGetGivenName() {
		$user = new \stdClass;
		$user->first_name = 'Test';

		$instance = Mockery::mock( __NAMESPACE__ . '\User' )->makePartial();
		$instance->shouldReceive( 'getUser' )->once()->andReturn( $user );

		$this->assertEquals( $user->first_name, $instance->getGivenName() );
	}

	public function testGetImage() {
		$instance = Mockery::mock( __NAMESPACE__ . '\User' )->makePartial();

		M::userFunction( 'get_avatar_url', array(
			'times'  => 1,
			'args'   => array( 1, '*' ),
			'return' => 'http://example.com/image.jpg',
		) );

		$this->assertEquals( 'http://example.com/image.jpg', $instance->getImage( 1 ) );
	}

	public function testGetName() {
		$user = new \stdClass;
		$user->display_name = 'Test McTest';

		$instance = Mockery::mock( __NAMESPACE__ . '\User' )->makePartial();
		$instance->shouldReceive( 'getUser' )->once()->andReturn( $user );

		$this->assertEquals( $user->display_name, $instance->getName( 1 ) );
	}

	public function testGetUrl() {
		$user = new \stdClass;
		$user->user_url = 'http://example.com';

		$instance = Mockery::mock( __NAMESPACE__ . '\User' )->makePartial();
		$instance->shouldReceive( 'getUser' )->once()->andReturn( $user );

		$this->assertEquals( $user->user_url, $instance->getUrl( 1 ) );
	}
}
