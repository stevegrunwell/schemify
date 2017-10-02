<?php
/**
 * Core functionality for Schemify.
 *
 * @package Schemify
 */

namespace Schemify\Core;

/**
 * Build the JSON-LD object for the given post.
 *
 * @throws \Exception If the specified Schema class doesn't exist.
 *
 * @param int    $post_id     Optional. The post ID to build the Schema object for. The default is
 *                            the current post.
 * @param string $object_type Optional. The type of object to construct a schema for (post, user,
 *                            etc.). Default is 'post'.
 * @return Schema\Thing An instance of a Schema\Thing or one of its sub-classes.
 */
function build_object( $post_id = 0, $object_type = 'post' ) {
	if ( ! $post_id ) {
		$post_id = get_the_ID();
	}

	$schema = get_schema_name( $post_id, $object_type );
	$class  = '\\Schemify\\Schemas\\' . $schema;

	try {
		if ( ! class_exists( $class ) ) {
			throw new \Exception( esc_html( sprintf(
				__( 'Class %s does not exist', 'schemify' ), $class
			) ) );
		}
		$instance = new $class( $post_id, true );

	} catch ( \Exception $e ) {
		$instance = new \Schemify\Schemas\Thing( $post_id, true );

		trigger_error( esc_html( sprintf(
			__( 'Unable to find schema "%s", falling back to "Thing"', 'schemify' ),
			esc_attr( $schema )
		) ), E_USER_WARNING );
	}

	return $instance->getProperties();
}

/**
 * Helper function get the base MIME-type for an attachment by ID.
 *
 * @param int $attachment_id The attachment ID.
 * @return string One of the following: 'image', 'video', 'audio', or 'other'.
 */
function get_attachment_type( $attachment_id ) {
	$mime  = get_post_mime_type( $attachment_id );
	$bases = array( 'image', 'video', 'audio' );
	$base  = false;

	if ( $mime ) {
		$base = strtolower( substr( $mime, 0, strpos( $mime, '/' ) ) );
	}

	return $base && in_array( $base, $bases, true ) ? $base : 'other';
}

/**
 * Determine the schema that should be used for the given post ID.
 *
 * @param int    $object_id   The post ID.
 * @param string $object_type The type of object being built.
 * @return string The type of schema to use for this object.
 */
function get_schema_name( $object_id, $object_type ) {
	$post_type = 'post' === $object_type ? get_post_type( $object_id ) : '';
	$schema    = 'Thing';

	/**
	 * Modify the Schema used to represent the given post type.
	 *
	 * @param string $schema      The schema to use for this post.
	 * @param string $object_type The type of object being constructed.
	 * @param string $post_type   The object's post type. If $object_type is not equal to 'post'
	 *                            this will be an empty string.
	 * @param int    $object_id   The post ID.
	 */
	return apply_filters( 'schemify_schema', $schema, $object_type, $post_type, $object_id );
}

/**
 * Create a new MediaObject instance based on an attachment URL.
 *
 * This function works by attempting to find the corresponding attachment ID, then building the
 * object normally. In order to do so, we're performing a direct SQL query, `since url_to_postid()`
 * won't work with attachment pretty permalinks.
 *
 * @link https://pippinsplugins.com/retrieve-attachment-id-from-image-url/
 *
 * @global $wpdb
 *
 * @param string $url    The URL for the attachment that we're building a MediaObject for.
 * @param string $schema Optional. The sub-class of MediaObject to build. Default is 'MediaObject'.
 * @return MediaObject|null Either a MediaObject (or sub-class) instance or NULL if the attachment
 *                          ID cannot be determined.
 */
function get_media_object_by_url( $url, $schema = 'MediaObject' ) {
	global $wpdb;

	$attachment_id = $wpdb->get_var( $wpdb->prepare(
		"SELECT ID FROM $wpdb->posts WHERE guid = '%s';",
		$url
	) );

	if ( ! $attachment_id ) {
		return;
	}

	$class = '\\Schemify\\Schemas\\' . $schema;

	return new $class( $attachment_id );
}

/**
 * Output the JSON-LD for the given post.
 *
 * @param int    $post_id     Optional. The post ID to get the Schema object for. The default is
 *                            the current post.
 * @param string $object_type Optional. The type of object to construct a schema for (post, user,
 *                            etc.). Default is 'post'.
 */
function get_json( $post_id = 0, $object_type = 'post' ) {
	$object = build_object( $post_id, $object_type );
	$cached = false;
	if ( isset( $object['cached'] ) ) {
		$cached = $object['cached'];
		unset( $object['cached'] );
	}
?>

<script type="application/ld+json">
<?php
	echo wp_kses_post( wp_json_encode( $object, JSON_PRETTY_PRINT ) . PHP_EOL );
	if ( false !== $cached ) {
		echo sprintf( '<!-- Cached: %s -->', esc_js( $cached ) );
	}
?>
</script>

<?php
}

/**
 * Utility function to strip namespaces from a class name.
 *
 * @link http://stackoverflow.com/a/27457689/329911
 *
 * @param string $class The full class name.
 * @return string The class name, devoid of namespaces.
 */
function strip_namespace( $class ) {
	$index = strrchr( $class, '\\' );

	return $index ? substr( $index, 1 ) : $class;
}
