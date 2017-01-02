<?php
/**
 * A representation of a WordPress user.
 *
 * @package Schemify
 * @link    http://schema.org/Person
 */

namespace Schemify\Schemas\WP;

use Schemify\Schemas as Schemas;

class User extends Schemas\Person {

	use Traits\Schema;

	/**
	 * A cache of the user data.
	 *
	 * @var WP_User $user
	 */
	protected $user;

	/**
	 * Retrieve the user object from the cache.
	 *
	 * @return WP_User The cached WP_User object.
	 */
	public function getUser() {
		if ( ! $this->user ) {
			$this->user = get_user_by( 'id', $this->postId );
		}

		return $this->user;
	}

	/**
	 * Get the user's bio as his/her description.
	 *
	 * @param int $user_id The user ID. This parameter isn't actually used, but is required so the
	 *                     method signature matches that of Thing::getDescription().
	 * @return string The user's bio.
	 */
	public function getDescription( $user_id ) {
		return $this->getUser()->description;
	}

	/**
	 * Get the user's family (last) name.
	 *
	 * @return string The user's family name.
	 */
	public function getFamilyName() {
		return $this->getUser()->last_name;
	}

	/**
	 * Get the user's given (first) name.
	 *
	 * @return string The user's given name.
	 */
	public function getGivenName() {
		return $this->getUser()->first_name;
	}

	/**
	 * Get the user's avatar.
	 *
	 * @param int $user_id The user ID.
	 * @return string The user's avatar.
	 */
	public function getImage( $user_id ) {
		return get_avatar_url( $user_id, array(
			'size' => 600,
		) );
	}

	/**
	 * Get the user's display name.
	 *
	 * @param int $user_id The user ID. This parameter isn't actually used, but is required so the
	 *                     method signature matches that of Thing::getName().
	 * @return string The user's display name.
	 */
	public function getName( $user_id ) {
		return $this->getUser()->display_name;
	}

	/**
	 * Get the user's URL.
	 *
	 * @param int $user_id The user ID. This parameter isn't actually used, but is required so the
	 *                     method signature matches that of Thing::getUrl().
	 * @return string The user's URL, if provided.
	 */
	public function getUrl( $user_id ) {
		return $this->getUser()->user_url;
	}
}
