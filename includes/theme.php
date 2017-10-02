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
 * Set default Schemas for the default post types.
 *
 * @param string $schema      The schema to use for this post.
 * @param string $object_type The type of object being constructed.
 * @param string $post_type   The current post's post_type, or any empty string if $object_type is
 *                            not equal to 'post'.
 * @param int    $object_id   The object ID.
 */
function set_default_schemas( $schema, $object_type, $post_type, $object_id ) {

	// Users are easy.
	if ( 'user' === $object_type ) {
		return 'WP\User';
	}

	switch ( $post_type ) {
		case 'post':
			$schema = 'BlogPosting';
			break;

		case 'page':
			$schema = 'WebPage';
			break;

		case 'attachment':
			$mime = Core\get_attachment_type( $object_id );

			if ( 'image' === $mime ) {
				$schema = 'ImageObject';
			} else {
				$schema = 'MediaObject';
			}
			break;
	}

	// Collect values from $wp_post_types.
	$post_type_schemas = get_cpt_schemas();

	if ( isset( $post_type_schemas[ $post_type ] ) ) {
		$schema = $post_type_schemas[ $post_type ];
	}

	// The homepage should be a WebSite.
	if ( is_front_page() || is_home() ) {
		$schema = 'WP\WebSite';

	} elseif ( is_search() ) {
		$schema = 'SearchResultsPage';
	}

	return $schema;
}
add_filter( 'schemify_schema', __NAMESPACE__ . '\set_default_schemas', 1, 4 );

/**
 * Appends the JSON-LD object to the site footer.
 */
function append_to_footer() {
	$object_id   = get_queried_object_id();
	$object_type = 'post';

	// Return early on singular posts for unsupported post types.
	if ( is_singular() && ! post_type_supports( get_post_type(), 'schemify' ) ) {
		return;

	} elseif ( is_front_page() ) {
		$object_id = 'front';

	} elseif ( is_home() ) {
		$object_id = 'home';

	} elseif ( is_author() ) {
		$object_id   = get_the_author_meta( 'ID' );
		$object_type = 'user';

	} elseif ( is_search() ) {
		$object_id   = 'search';
		$object_type = 'search';
	}

	Core\get_json( $object_id, $object_type );
}
add_action( 'wp_footer', __NAMESPACE__ . '\append_to_footer' );

/**
 * Collect post type => schema mappings for custom post types.
 *
 * For convenience, developers can automatically assign a schema to custom post types via the
 * 'schemify_schema' property on the post object.
 *
 * @return array A mapping of post type => schema for any post types with 'schemify_schema' props.
 */
function get_cpt_schemas() {
	$post_types = get_post_types( null, 'object' );
	$schemas    = array();

	foreach ( $post_types as $post_type ) {
		if ( isset( $post_type->schemify_schema ) && $post_type->schemify_schema ) {
			$schemas[ $post_type->name ] = $post_type->schemify_schema;
		}
	}

	return $schemas;
}
