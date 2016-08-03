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
		$this->assertEquals( 'BlogPosting', set_default_schemas( 'Thing', 'post', 123 ) );
	}

	public function testSetDefaultSchemasForImages() {
		M::wpFunction( 'Schemify\Core\get_attachment_type', array(
			'times'  => 1,
			'args'   => array( 123 ),
			'return' => 'image',
		) );

		$this->assertEquals( 'ImageObject', set_default_schemas( 'Thing', 'attachment', 123 ) );
	}

	public function testAppendToSingularFooter() {
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

		M::wpFunction( 'get_the_ID', array(
			'times'  => 1,
			'return' => 123,
		) );

		M::wpFunction( 'Schemify\Core\get_json', array(
			'times'  => 1,
			'args'   => array( 123 ),
		) );

		append_to_singular_footer();
	}

	public function testAppendToSingularFooterOnNonSingularPages() {
		M::wpFunction( 'is_singular', array(
			'return' => false,
		) );

		M::wpFunction( 'Schemify\Core\get_json', array(
			'times'  => 0,
		) );

		append_to_singular_footer();
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

		append_to_singular_footer();
	}
}
