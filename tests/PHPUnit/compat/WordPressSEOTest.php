<?php
/**
 * Tests for the Yoast SEO compatibility functions.
 *
 * @link https://wordpress.org/plugins/wordpress-seo/
 *
 * @package Schemify
 */

namespace Schemify\Compat\WordPressSEO;

use WP_Mock as M;
use Schemify;

class WordPressSEOTest extends Schemify\TestCase {

	protected $testFiles = array(
		'compat/wordpress-seo.php',
	);

	public function testAddUserProfileUrls() {
		M::wpFunction( 'get_user_meta', array(
			'args'   => array( 1, 'googleplus', true ),
			'return' => 'http://plus.google.com',
		) );

		M::wpFunction( 'get_user_meta', array(
			'args'   => array( 1, 'facebook', true ),
			'return' => 'http://facebook.com',
		) );

		M::wpFunction( 'get_user_meta', array(
			'args'   => array( 1, 'twitter', true ),
			'return' => 'twitter_user',
		) );

		M::wpPassthruFunction( 'esc_url' );

		$this->assertEquals(
			array(
				'sameAs' => array(
					'http://plus.google.com',
					'http://facebook.com',
					'https://twitter.com/twitter_user',
				),
			),
			add_user_profile_urls( array(), 'Person', 1 )
		);
	}

	public function testAddUserProfileUrlsStripsEmptyResults() {
		M::wpFunction( 'get_user_meta', array(
			'args'   => array( 1, 'googleplus', true ),
			'return' => '',
		) );

		M::wpFunction( 'get_user_meta', array(
			'times'  => 1,
			'args'   => array( 1, 'facebook', true ),
			'return' => 'http://facebook.com',
		) );

		M::wpFunction( 'get_user_meta', array(
			'args'   => array( 1, 'twitter', true ),
			'return' => '',
		) );

		M::wpPassthruFunction( 'esc_url', array() );

		$this->assertEquals(
			array(
				'sameAs' => array(
					'http://facebook.com',
				),
			),
			add_user_profile_urls( array(), 'Person', 1 )
		);
	}

	/**
	 * Yoast SEO stores the Twitter handle, not the full Twitter URL.
	 */
	public function testAddUserProfileUrlsHandlesTwitterUrls() {
		M::wpFunction( 'get_user_meta', array(
			'args'   => array( 1, 'twitter', true ),
			'return' => 'twitter_user',
		) );

		M::wpFunction( 'get_user_meta', array(
			'return' => '',
		) );

		M::wpPassthruFunction( 'esc_url', array() );

		$this->assertEquals(
			array(
				'sameAs' => array(
					'https://twitter.com/twitter_user',
				),
			),
			add_user_profile_urls( array(), 'Person', 1 )
		);
	}

	public function testAddUserProfileUrlsMergesWithOtherResults() {
		M::wpFunction( 'get_user_meta', array(
			'args'   => array( 1, 'facebook', true ),
			'return' => 'https://facebook.com',
		) );

		M::wpFunction( 'get_user_meta', array(
			'return' => '',
		) );

		M::wpPassthruFunction( 'esc_url', array() );

		$this->assertEquals(
			array(
				'sameAs' => array(
					'foo',
					'bar',
					'https://facebook.com',
				),
			),
			add_user_profile_urls( array( 'sameAs' => array( 'foo', 'bar' ) ), 'Person', 1 )
		);
	}
}
