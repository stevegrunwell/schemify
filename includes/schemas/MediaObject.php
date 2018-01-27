<?php
/**
 * The MediaObject Schema.
 *
 * @package Schemify
 * @link    http://schema.org/MediaObject
 */

namespace Schemify\Schemas;

use Schemify\Core as Core;

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
	 * Get the associated (attached) post object.
	 *
	 * @throws \Exception When unable to load a schema.
	 *
	 * @param int $post_id The post ID.
	 * @return CreativeWork|null A CreativeWork object or null if the attachment is unattached.
	 */
	public function getAssociatedArticle( $post_id ) {
		if ( ! $this->isMain ) {
			return null;
		}

		$parent_post_id = wp_get_post_parent_id( $post_id );

		// Return early if the media is unattached.
		if ( ! $parent_post_id ) {
			return;
		}

		// Proceed building the parent post's schema.
		$schema   = __NAMESPACE__ . '\\' . Core\get_schema_name( $parent_post_id, 'post' );
		$instance = null;

		try {
			if ( ! class_exists( $schema ) ) {
				throw new \Exception( esc_html__( 'Schema is not defined', 'schemify' ) );
			}

			$instance = new $schema( $parent_post_id );

		} catch ( \Exception $e ) {
			trigger_error( esc_html( sprintf(
				/* Translators: %1$s is the schema name, %2$s is the error message. */
				__( 'Unable to load schema %1$s: %2$s', 'schemify' ),
				$schema,
				$e->getMessage()
			) ), E_USER_WARNING );
		}

		return $instance;
	}

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
