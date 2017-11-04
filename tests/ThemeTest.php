<?php
/**
 * Tests for the theme integration.
 *
 * @package Schemify
 */

use Schemify\Theme as Theme;

class ThemeTest extends WP_UnitTestCase {

	public function testRegistersThemeSupport() {
		$post_types = [ 'post', 'page', 'attachment' ];

		Theme\register_post_type_support();

		foreach ( $post_types as $post_type ) {
			$this->assertTrue(
				post_type_supports( $post_type, 'schemify' ),
				sprintf( 'Expected "%s" post type to support Schemify', $post_type )
			);
		}
	}

	/**
	 * @dataProvider defaultSchemaProvider()
	 */
	public function testSetsDefaultSchemas( $object_type, $post_type, $expected ) {
		$this->assertEquals(
			$expected,
			Theme\set_default_schemas( 'Thing', $object_type, $post_type, null )
		);
	}

	public function defaultSchemaProvider() {
		return [
			'User' => [ 'user', null, 'WP\User' ],
			'Post' => [ 'post', 'post', 'BlogPosting' ],
			'Page' => [ 'post', 'page', 'WebPage' ],
		];
	}

	public function testSetsDefaultSchemaForImages() {
		$image = self::factory()->attachment->create_object( 'image.jpg', 0, [
			'post_mime_type' => 'image/jpeg',
		] );

		$this->assertEquals(
			'ImageObject',
			Theme\set_default_schemas( 'Thing', 'post', 'attachment', $image ),
			'Images should be represented by the ImageObject schema.'
		);
	}

	public function testSetsDefaultSchemaForOtherMediaTypes() {
		$media = self::factory()->attachment->create_object( 'image.mp4', 0, [
			'post_mime_type' => 'video/mp4',
		] );

		$this->assertEquals(
			'MediaObject',
			Theme\set_default_schemas( 'Thing', 'post', 'attachment', $media ),
			'Other media should be represented by the MediaObject schema.'
		);
	}

	public function testSetsDefaultSchemaForCustomPostTypes() {
		register_post_type( 'test-cpt', [
			'schemify_schema' => 'SomeSchema',
		] );

		$this->assertEquals(
			'SomeSchema',
			Theme\set_default_schemas( 'Thing', 'post', 'test-cpt', null ),
			'Custom post types\' schemas are determined by get_cpt_schemas().'
		);
	}

	public function testSetsDefaultSchemaAcceptsUpdatedPostTypes() {
		global $wp_post_types;

		$wp_post_types['post']->schemify_schema = 'SomeSchema';

		$this->assertEquals(
			'SomeSchema',
			Theme\set_default_schemas( 'Thing', 'post', 'post', null ),
			'Check core post types for modified default schemas'
		);

		// Reset the value.
		$wp_post_types['post']->schemify_schema = null;
	}

	/**
	 * @dataProvider specialPageProvider()
	 */
	public function testSetsDefaultSchemasForSpecialPages( $url, $precondition, $expected ) {
		$this->go_to( $url );

		$this->assertTrue( call_user_func( $precondition ), 'Precondition check failed.' );

		$this->assertEquals(
			$expected,
			Theme\set_default_schemas( 'Thing', null, null, null )
		);
	}

	public function specialPageProvider() {
		return [
			'Home' => [ home_url(), 'is_home', 'WP\WebSite' ],
			'Front' => [ site_url(), 'is_front_page', 'WP\WebSite' ],
			'Search' => [ get_search_link( 'foo' ), 'is_search', 'SearchResultsPage' ],
		];
	}

	public function testAppendToFooter() {
		$post = $this->factory()->post->create();
		$this->go_to( get_permalink( $post ) );

		ob_start();
		Theme\append_to_footer();
		$output = ob_get_clean();

		$this->assertContains( '<script type="application/ld+json">', $output );
	}

	public function testAppendToFooterIgnoresSingularPostsThatDoNotSupportSchemify() {
		$post = $this->factory()->post->create();
		remove_post_type_support( 'post', 'schemify' );
		$this->go_to( get_permalink( $post ) );

		ob_start();
		Theme\append_to_footer();
		$output = ob_get_clean();

		$this->assertNotContains(
			'<script type="application/ld+json">',
			$output,
			'Post types that don\'t support Schemify should not print Schema data.'
		);
	}

	public function testGetCptSchemas() {
		register_post_type( 'test-cpt', [
			'schemify_schema' => 'SomeSchema',
		] );

		$this->assertEquals([ 'test-cpt' => 'SomeSchema' ], Theme\get_cpt_schemas() );
	}
}
