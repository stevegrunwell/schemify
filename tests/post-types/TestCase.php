<?php
/**
 * Base test for testing the output of different post types.
 *
 * @package Schemify
 */

namespace Test\PostTypes;

use WP_UnitTestCase;

class TestCase extends WP_UnitTestCase {

	/**
	 * Verify the structure of a Person object.
	 *
	 * @param Person $person
	 * @param int    $user_id Optional. The WordPress user ID this person represents, used for
	 *                        comparing values. Default is empty.
	 */
	protected function assertPerson( $person, $user_id = null ) {
		$this->assertEquals( 'Person', $person->getSchema() );

		if ( $user_id ) {
			$user = get_user_by( 'id', $user_id );

			$this->assertEquals( $user->display_name, $person->getProp( 'name' ) );
			$this->assertEquals( $user->user_url, $person->getProp( 'url' ) );
			$this->assertEquals( get_avatar_url( $user_id ), $person->getProp( 'image' ) );
		}
	}

	/**
	 * Verify the structure of a Person object.
	 *
	 * @param Organization $organization
	 */
	protected function assertOrganization( $organization ) {
		$this->assertEquals( 'Organization', $organization->getSchema() );
	}
}
