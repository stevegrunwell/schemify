<?php
/**
 * Tests for the MediaObject schema.
 *
 * @package Schemify
 */

namespace Schemify\Schemas;

use WP_Mock as M;

use Mockery;
use ReflectionMethod;
use ReflectionProperty;
use Schemify;

class MediaObjectTest extends Schemify\TestCase {

	protected $testFiles = array(
		'schemas.php',
	);

	public function testGetAssociatedArticle() {
		$instance = new MediaObject( 123, true );

		M::wpFunction( 'wp_get_post_parent_id', array(
			'times'  => 1,
			'args'   => array( 123 ),
			'return' => 111,
		) );

		M::wpFunction( 'Schemify\Core\get_schema_name', array(
			'times'  => 1,
			'args'   => array( 111 ),
			'return' => 'CreativeWork',
		) );

		$this->assertInstanceOf( __NAMESPACE__ . '\CreativeWork', $instance->getAssociatedArticle( 123 ) );
	}

	/**
	 * @expectedException PHPUnit_Framework_Error_Warning
	 */
	public function testGetAssociatedArticleHandlesMissingSchemas() {
		$instance = new MediaObject( 123, true );

		M::wpFunction( 'wp_get_post_parent_id', array(
			'return' => 111,
		) );

		M::wpFunction( 'Schemify\Core\get_schema_name', array(
			'return' => 'ThisIsNotAClass',
		) );

		M::wpPassthruFunction( '__' );
		M::wpPassthruFunction( 'esc_html' );
		M::wpPassthruFunction( 'esc_html__' );

		$this->assertNull( $instance->getAssociatedArticle( 123 ) );
	}

	public function testGetContentUrl() {
		$instance = new MediaObject( 123 );

		M::wpFunction( 'wp_get_attachment_url', array(
			'times'  => 1,
			'args'   => array( 123 ),
			'return' => 'URL',
		) );

		$this->assertEquals( 'URL', $instance->getContentUrl( 123 ) );
	}

	public function testGetThumbnailUrl() {
		$instance = new MediaObject( 123 );

		M::wpFunction( 'wp_get_attachment_image_src', array(
			'times'  => 1,
			'args'   => array( 123, 'thumbnail', true ),
			'return' => array( 'URL', 150, 150 ),
		) );

		$this->assertEquals( 'URL', $instance->getThumbnailUrl( 123 ) );
	}

	public function testGetThumbnailUrlReturnsNullIfThumbnailIsNotFound() {
		$instance = new MediaObject( 123 );

		M::wpFunction( 'wp_get_attachment_image_src', array(
			'times'  => 1,
			'args'   => array( 123, 'thumbnail', true ),
			'return' => false,
		) );

		$this->assertNull( $instance->getThumbnailUrl( 123 ) );
	}
}
