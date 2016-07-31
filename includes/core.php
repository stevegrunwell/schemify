<?php
/**
 * Core functionality for Schemify.
 *
 * @package Schemify
 */

namespace Schemify\Core;

/**
 * Build the JSON+LD object for the given post.
 *
 * @param int $post_id Optional. The post ID to build the Schema object for. The default is the
 *                     current post.
 * @return Schema\Thing An instance of a Schema\Thing or one of its sub-classes.
 */
function build_object( $post_id = 0 ) {
	if ( ! $post_id ) {
		$post_id = get_the_ID();
	}

	$post_type = get_post_type( $post_id );
	$schema    = 'Thing';

	/**
	 * Modify the Schema used to represent the given post type.
	 *
	 * @param string $schema    The schema to use for this post.
	 * @param string $post_type The current post's post_type.
	 */
	$schema = apply_filters( 'schemify_schema', $schema, $post_type );
	$class  = '\\Schemify\\Schemas\\' . $schema;

	try {
		$instance = new $class( $post_id, true );

	} catch ( \Exception $e ) {
		trigger_error( sprintf(
			esc_html__( 'Unable to find schema "%s", falling back to "Thing"', 'schemify' ),
			esc_attr( $schema )
		), E_USER_NOTICE );

		$instance = new \Schemify\Schemas\Thing( $post_id, true );
	}

	return $instance->getProperties();
}

/**
 * Output the JSON+LD for the given post.
 *
 * @param int $post_id Optional. The post ID to get the Schema object for. The default is the
 *                     current post.
 */
function get_json( $post_id = 0 ) {
	$object = build_object( $post_id );
?>

<script type="application/ld+json">
<?php echo wp_kses_post( wp_json_encode( $object, JSON_PRETTY_PRINT ) . PHP_EOL ); ?>
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
