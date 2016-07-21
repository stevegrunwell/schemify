<?php
/**
 * The MediaObject Schema.
 *
 * @package Schemify
 * @link    http://schema.org/MediaObject
 */

namespace Schemify\Schemas;

class MediaObject extends CreativeWork {

	/**
	 * The properties this schema may utilize.
	 *
	 * @var array $properties
	 */
	protected static $properties = array(
		'associatedArticle',
		'bitrate',
		'contentSize',
		'contentUrl',
		'duration',
		'embedUrl',
		'encodesCreativeWork',
		'encodingFormat',
		'expires',
		'height',
		'playerType',
		'productionCompany',
		'regionsAllowed',
		'requiresSubscription',
		'uploadDate',
		'width',
	);

	/**
	 * Get the actual content URL.
	 *
	 * @param int $post_id The attachment ID.
	 * @return string The content URL.
	 */
	public function getContentUrl( $post_id ) {
		return wp_get_attachment_url( $post_id );
	}

	/**
	 * Retrieve the thumbnail URL for a post.
	 *
	 * @param int $post_id The post ID.
	 * @return string The post's URL.
	 */
	public function getThumbnailUrl( $post_id ) {
		$thumbnail = wp_get_attachment_image_src( $post_id, 'thumbnail', true );

		return $thumbnail ? $thumbnail[0] : null;
	}
}
