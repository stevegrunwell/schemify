<?php
/**
 * Compatibility with Yoast SEO.
 *
 * @package Schemify
 */

namespace Schemify\Compat\WordPressSEO;

/**
 * Add social media profile URLs to WP\User objects.
 *
 * The social media URL fields on the user profile screen will be included in the user's 'sameAs'
 * schema property.
 *
 * @param array  $data      The collection of properties assembled for this object.
 * @param string $schema    The current schema being filtered.
 * @param int    $object_id The object ID being constructed.
 */
function add_user_profile_urls( $data, $schema, $object_id ) {
	$networks = array( 'googleplus', 'facebook' );
	$profiles = array();

	foreach ( $networks as $network ) {
		$profiles[] = get_user_meta( $object_id, $network, true );
	}

	// Twitter requires special construction.
	if ( $twitter = get_user_meta( $object_id, 'twitter', true ) ) {
		$profiles[] = sprintf( 'https://twitter.com/%s', $twitter );
	}

	// Ensure everything's escaped.
	$profiles = array_map( 'esc_url', array_values( array_filter( $profiles ) ) );

	$data['sameAs'] = isset( $data['sameAs'] ) ? array_merge( $data['sameAs'], $profiles ) : $profiles;

	return $data;
}
add_filter( 'schemify_get_properties_Person', __NAMESPACE__ . '\add_user_profile_urls', 10, 3 );
