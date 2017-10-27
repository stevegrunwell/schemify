<?php
/**
 * Caching layer for Schemify objects.
 *
 * @package Schemify
 */

namespace Schemify\Cache;

use Schemify\Schemas\Thing;

/**
 * Prepare a cache key for the given object.
 *
 * @param int|string $object_id The object ID, though this can also include strings like "home".
 *
 * @return string A cache key to be used with the WP object cache.
 */
function get_key( $object_id ) {
	return sprintf( 'schema_%s', $object_id );
}

/**
 * Flush the object cache for a given post when that post is updated.
 *
 * @param int $post_id The post being updated.
 */
function update_post_cache( $post_id ) {
	wp_cache_delete( get_key( $post_id ), 'schemify' );
}

add_action( 'save_post', __NAMESPACE__ . '\update_post_cache' );

/**
 * Flush the Schema representation of a user and any posts they've published when the user's
 * profile is updated.
 *
 * @param int $user_id The WordPress user ID.
 */
function update_user_cache( $user_id ) {
	$author_posts = get_posts( array(
		'no_found_rows'          => true,
		'update_post_meta_cache' => false,
		'update_post_term_cache' => false,
		'author'                 => $user_id,
		'fields'                 => 'ids',
		'posts_per_page'         => 5000,
	) );

	array_map( __NAMESPACE__ . '\update_post_cache', $author_posts );
}

add_action( 'profile_update', __NAMESPACE__ . '\update_user_cache' );


/**
 * Forces all schemify cache to be flushed, but does not actually flush them
 *
 * @param mixed $old_value The old option value.
 *
 * @return void
 */
function force_cache_flush( $old_value ) {
	wp_cache_set( 'schemify_last_update', time(), 'schemify', 0 );
}

add_action( 'update_option_blogdescription', __NAMESPACE__ . '\force_cache_flush' );
add_action( 'update_option_blogname', __NAMESPACE__ . 'force_cache_flush' );
