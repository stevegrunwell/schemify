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
 * @return array The filtered $data array.
 */
function add_user_profile_urls( $data, $schema, $object_id ) {
	$networks = array( 'googleplus', 'facebook' );
	$profiles = array();

	foreach ( $networks as $network ) {
		$profiles[] = get_user_meta( $object_id, $network, true );
	}

	// Twitter requires special construction.
	$twitter = get_user_meta( $object_id, 'twitter', true );
	if ( $twitter ) {
		$profiles[] = sprintf( 'https://twitter.com/%s', $twitter );
	}

	// Ensure everything's escaped.
	$profiles = array_map( 'esc_url', array_values( array_filter( $profiles ) ) );

	$data['sameAs'] = isset( $data['sameAs'] ) ? array_merge( $data['sameAs'], $profiles ) : $profiles;

	return $data;
}
add_filter( 'schemify_get_properties_Person', __NAMESPACE__ . '\add_user_profile_urls', 10, 3 );

/**
 * Use the default OpenGraph image for entities that don't have images applied to them.
 *
 * @param array  $data      The collection of properties assembled for this object.
 * @param string $schema    The current schema being filtered.
 * @param int    $object_id The object ID being constructed.
 * @param bool   $is_main   Is this the top-level JSON-LD schema being constructed.
 * @return array The possibly-filtered $data array.
 */
function set_default_image( $data, $schema, $object_id, $is_main ) {
	if ( ! isset( $data['image'] ) || ! empty( $data['image'] ) || ! $is_main ) {
		return $data;
	}

	$yoast_social = get_option( 'wpseo_social', array() );
	$image_url    = isset( $yoast_social['og_default_image'] ) ? $yoast_social['og_default_image'] : null;

	if ( is_front_page() && isset( $yoast_social['og_frontpage_image'] ) && $yoast_social['og_frontpage_image'] ) {
		$image_url = $yoast_social['og_frontpage_image'];
	}

	$data['image'] = \Schemify\Core\get_media_object_by_url( $image_url, 'ImageObject' );

	return $data;
}
add_filter( 'schemify_get_properties', __NAMESPACE__ . '\set_default_image', 10, 4 );
