<?php
/**
 * WP Admin views for Schemify.
 *
 * @package Schemify
 */

namespace Schemify\Admin;

use Schemify\Core as Core;

/**
 * Register the "Structured Data" meta box.
 */
function add_meta_boxes() {
	add_meta_box(
		'schemify',
		_x( 'Structured Data', 'meta box title', 'schemify' ),
		__NAMESPACE__ . '\meta_box_cb',
		'post'
	);
}
add_action( 'add_meta_boxes', __NAMESPACE__ . '\add_meta_boxes' );

/**
 * Callback to populate the "Structured Data" meta box.
 *
 * @param WP_Post $post The current post object.
 */
function meta_box_cb( $post ) {
	$post_type = get_post_type_object( $post->post_type );
?>
	<p class="description"><?php echo esc_html( sprintf(
		__( 'This is a preview of the JSON-LD object that will be appended to this %s:', 'schemify' ),
		$post_type->labels->singular_name
	) ); ?></p>
	<pre class="code" style="overflow: auto;"><?php
		echo wp_json_encode( Core\build_object( $post->ID ), JSON_PRETTY_PRINT );
	?></pre>

<?php
}
