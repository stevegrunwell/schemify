<?php
/**
 * Tests for the plugin's theme integrations.
 *
 * @package Schemify
 */

namespace Schemify\Theme;

use WP_Mock as M;
use Schemify;

class ThemeTest extends Schemify\TestCase {

	protected $testFiles = array(
		'theme.php',
	);

	public function testRegisterPostTypeSupport() {
		M::wpFunction( 'add_post_type_support', array(
			'times' => '1+',
			'args'  => array( '*', 'schemify' ),
		) );

		register_post_type_support();
	}

	public function testSetDefaultSchemasForPosts() {
		M::wpFunction( __NAMESPACE__ . '\get_cpt_schemas', array( 'return' => array() ) );
		M::wpFunction( 'is_front_page', array( 'return' => false ) );
		M::wpFunction( 'is_home', array( 'return' => false ) );

		$this->assertEquals( 'BlogPosting', set_default_schemas( 'Thing', 'post', 'post', 123 ) );
	}

	public function testSetDefaultSchemasForPages() {
		M::wpFunction( __NAMESPACE__ . '\get_cpt_schemas', array( 'return' => array() ) );
		M::wpFunction( 'is_front_page', array( 'return' => false ) );
		M::wpFunction( 'is_home', array( 'return' => false ) );

		$this->assertEquals( 'WebPage', set_default_schemas( 'Thing', 'post', 'page', 123 ) );
	}

	public function testSetDefaultSchemasForImages() {
		M::wpFunction( 'Schemify\Core\get_attachment_type', array(
			'times'  => 1,
			'args'   => array( 123 ),
			'return' => 'image',
		) );

		M::wpFunction( __NAMESPACE__ . '\get_cpt_schemas', array( 'return' => array() ) );
		M::wpFunction( 'is_front_page', array( 'return' => false ) );
		M::wpFunction( 'is_home', array( 'return' => false ) );

		$this->assertEquals( 'ImageObject', set_default_schemas( 'Thing', 'post', 'attachment', 123 ) );
	}

	public function testSetDefaultSchemasForOtherMediaTypes() {
		M::wpFunction( 'Schemify\Core\get_attachment_type', array(
			'return' => 'other',
		) );

		M::wpFunction( __NAMESPACE__ . '\get_cpt_schemas', array( 'return' => array() ) );
		M::wpFunction( 'is_front_page', array( 'return' => false ) );
		M::wpFunction( 'is_home', array( 'return' => false ) );

		$this->assertEquals( 'MediaObject', set_default_schemas( 'Thing', 'post', 'attachment', 123 ) );
	}

	public function testSetDefaultSchemasForCustomPostTypes() {
		M::wpFunction( __NAMESPACE__ . '\get_cpt_schemas', array(
			'return' => array( 'cpt' => 'MySchema' ),
		) );
		M::wpFunction( 'is_front_page', array( 'return' => false ) );
		M::wpFunction( 'is_home', array( 'return' => false ) );

		$this->assertEquals( 'MySchema', set_default_schemas( 'Thing', 'post', 'cpt', 123 ) );
	}

	/**
	 * If someone modifies $wp_post_types for core post types, favor those schemify_schema values
	 * over the defaults we set.
	 *
	 * Basically, make sure we check the output of get_cpt_schemas() *after* our generic switch().
	 */
	public function testSetDefaultSchemasFavorsWPPostTypesOverSchemifyDefaults() {
		M::wpFunction( __NAMESPACE__ . '\get_cpt_schemas', array(
			'return' => array( 'post' => 'MySchema' ),
		) );
		M::wpFunction( 'is_front_page', array( 'return' => false ) );
		M::wpFunction( 'is_home', array( 'return' => false ) );

		$this->assertEquals( 'MySchema', set_default_schemas( 'Thing', 'post', 'post', 123 ) );
	}

	public function testSetDefaultSchemasForFrontPage() {
		M::wpFunction( __NAMESPACE__ . '\get_cpt_schemas', array( 'return' => array() ) );
		M::wpFunction( 'is_front_page', array(
			'return' => true,
		) );

		$this->assertEquals( 'WP\WebSite', set_default_schemas( 'Thing', 'post', 'page', 123 ) );
	}

	public function testSetDefaultSchemasForHomepage() {
		M::wpFunction( __NAMESPACE__ . '\get_cpt_schemas', array( 'return' => array() ) );
		M::wpFunction( 'is_front_page', array(
			'return' => false,
		) );

		M::wpFunction( 'is_home', array(
			'return' => true,
		) );

		$this->assertEquals( 'WP\WebSite', set_default_schemas( 'Thing', 'post', 'page', 123 ) );
	}

	public function testSetDefaultSchemasForUsers() {
		$this->assertEquals( 'WP\User', set_default_schemas( 'Thing', 'user', '', 123 ) );
	}

	public function testAppendToFooter() {
		M::wpFunction( 'get_the_ID', array(
			'times'  => 1,
			'return' => 123,
		) );

		M::wpFunction( 'is_singular', array(
			'times'  => 1,
			'return' => true,
		) );

		M::wpFunction( 'get_post_type', array(
			'times'  => 1,
			'return' => 'post',
		) );

		M::wpFunction( 'post_type_supports', array(
			'times'  => 1,
			'args'   => array( 'post', 'schemify' ),
			'return' => true,
		) );

		M::wpFunction( 'is_front_page', array(
			'return' => false,
		) );

		M::wpFunction( 'is_home', array(
			'return' => false,
		) );

		M::wpFunction( 'is_author', array(
			'return' => false,
		) );

		M::wpFunction( 'Schemify\Core\get_json', array(
			'times'  => 1,
			'args'   => array( 123, 'post' ),
		) );

		append_to_footer();
	}

	public function testAppendToFooterOnFrontPage() {
		M::wpFunction( 'is_singular', array(
			'return' => false,
		) );

		M::wpFunction( 'is_front_page', array(
			'return'  => true,
		) );

		M::wpFunction( 'Schemify\Core\get_json', array(
			'times'  => 1,
			'args'   => array( 'front', 'post' ),
		) );

		append_to_footer();
	}

	public function testAppendToFooterOnHomePage() {
		M::wpFunction( 'is_singular', array(
			'return' => false,
		) );

		M::wpFunction( 'is_front_page', array(
			'return'  => false,
		) );

		M::wpFunction( 'is_home', array(
			'return'  => true,
		) );

		M::wpFunction( 'Schemify\Core\get_json', array(
			'times'  => 1,
			'args'   => array( 'home', 'post' ),
		) );

		append_to_footer();
	}

	public function testAppendToFooterForAuthorArchives() {
		M::wpFunction( 'is_singular', array(
			'return' => false,
		) );

		M::wpFunction( 'is_front_page', array(
			'return'  => false,
		) );

		M::wpFunction( 'is_home', array(
			'return'  => false,
		) );

		M::wpFunction( 'is_author', array(
			'return' => true,
		) );

		M::wpFunction( 'get_the_author_meta', array(
			'args'   => array( 'ID' ),
			'return' => 42,
		) );

		M::wpFunction( 'Schemify\Core\get_json', array(
			'times'  => 1,
			'args'   => array( 42, 'user' ),
		) );

		append_to_footer();
	}

	public function testAppendToSingularFooterWithUnsupportedPostType() {
		M::wpFunction( 'is_singular', array(
			'return' => true,
		) );

		M::wpFunction( 'get_post_type', array(
			'return' => 'post',
		) );

		M::wpFunction( 'post_type_supports', array(
			'return' => false,
		) );

		M::wpFunction( 'Schemify\Core\get_json', array(
			'times'  => 0,
		) );

		append_to_footer();
	}

	public function testGetCPTSchemas() {
		$post                 = new \stdClass;
		$post->name           = 'post';
		$cpt                  = new \stdClass;
		$cpt->name            = 'cpt';
		$cpt->schemify_schema = 'CustomSchema';

		M::wpFunction( 'get_post_types', array(
			'return' => array( $post, $cpt ),
		) );

		$this->assertEquals( array( 'cpt' => 'CustomSchema' ), get_cpt_schemas() );
	}

	public function testGetCPTSchemasReturnsEmptyArrayIfUnused() {
		$post       = new \stdClass;
		$post->name = 'post';

		M::wpFunction( 'get_post_types', array(
			'return' => array( $post ),
		) );

		$this->assertEquals( array(), get_cpt_schemas() );
	}
}
