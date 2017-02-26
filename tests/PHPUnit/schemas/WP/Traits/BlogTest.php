<?php
/**
 * Tests for the WP\Organization schema.
 *
 * @package Schemify
 */

namespace Schemify\Schemas\WP;

use WP_Mock as M;

use Mockery;
use ReflectionMethod;
use ReflectionProperty;
use Schemify;

class OrganizationTest extends Schemify\TestCase {

	protected $testFiles = array(
		'schemas.php',
	);

	public function testGetDescription() {
		require_once ABSPATH . 'TestBlogTrait.php';

		$instance = new TestBlogTrait( 1 );

		M::userFunction( 'get_bloginfo', array(
			'times'  => 1,
			'args'   => array( 'description' ),
			'return' => 'My description',
		) );

		$this->assertEquals( 'My description', $instance->getDescription( 1 ) );
	}

	public function testGetImage() {
		require_once ABSPATH . 'TestBlogTrait.php';

		$instance = new TestBlogTrait( 1 );

		M::userFunction( 'get_site_icon_url', array(
			'times'  => 1,
			'args'   => array( null, null, 1 ),
			'return' => 'http://example.com/image.jpg',
		) );

		$this->assertEquals( 'http://example.com/image.jpg', $instance->getImage( 1 ) );
	}

	public function testGetLogo() {
		require_once ABSPATH . 'TestBlogTrait.php';

		$uniqid   = uniqid();
		$instance = Mockery::mock( __NAMESPACE__ . '\TestBlogTrait' )->makePartial();
		$instance->shouldReceive( 'getImage' )
			->once()
			->with( 123 )
			->andReturn( $uniqid );

		$this->assertEquals( $uniqid, $instance->getLogo( 123 ) );
	}

	public function testGetName() {
		require_once ABSPATH . 'TestBlogTrait.php';

		$instance = new TestBlogTrait( 1 );

		M::userFunction( 'get_bloginfo', array(
			'times'  => 1,
			'args'   => array( 'name' ),
			'return' => 'My name',
		) );

		$this->assertEquals( 'My name', $instance->getName( 1 ) );
	}

	public function testGetUrl() {
		require_once ABSPATH . 'TestBlogTrait.php';

		$instance = new TestBlogTrait( 1 );

		M::userFunction( 'get_bloginfo', array(
			'times'  => 1,
			'args'   => array( 'url' ),
			'return' => 'http://example.com',
		) );

		$this->assertEquals( 'http://example.com', $instance->getUrl( 1 ) );
	}
}
