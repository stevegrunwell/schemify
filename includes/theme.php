<?php
/**
 * Theme integration points.
 *
 * @package Schemify
 */

namespace Schemify\Theme;

use Schemify\Core as Core;

/**
 * Register default post type support for Schemify.
 */
function register_post_type_support() {
	$post_types = array( 'post', 'page', 'attachment' );

	foreach ( $post_types as $post_type ) {
		add_post_type_support( $post_type, 'schemify' );
	}
}
add_action( 'after_setup_theme', __NAMESPACE__ . '\register_post_type_support' );

/**
 * Appends the JSON+LD object to the footer of a singular post.
 */
function append_to_singular_footer() {
	if ( ! is_singular() || ! post_type_supports( get_post_type(), 'schemify' ) ) {
		return;
	}

	Core\get_json( get_the_ID() );
}
add_action( 'wp_footer', __NAMESPACE__ . '\append_to_singular_footer' );
