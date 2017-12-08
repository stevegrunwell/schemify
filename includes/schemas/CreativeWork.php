<?php
/**
 * The CreativeWork Schema.
 *
 * @package Schemify
 * @link    http://schema.org/CreativeWork
 */

namespace Schemify\Schemas;

class CreativeWork extends Thing {

	/**
	 * The properties this schema may utilize.
	 *
	 * @var array $properties
	 */
	protected static $properties = array(
		'about',
		'accessMode',
		'accessModeSufficient',
		'accessibilityAPI',
		'accessibilityControl',
		'accessibilityFeature',
		'accessibilityHazard',
		'accessibilitySummary',
		'accountablePerson',
		'aggregateRating',
		'alternativeHeadline',
		'associatedMedia',
		'audience',
		'audio',
		'author',
		'award',
		'character',
		'citation',
		'comment',
		'commentCount',
		'contentLocation',
		'contentRating',
		'contributor',
		'copyrightHolder',
		'copyrightYear',
		'creator',
		'dateCreated',
		'dateModified',
		'datePublished',
		'discussionUrl',
		'editor',
		'educationalAlignment',
		'educationalUse',
		'encoding',
		'exampleOfWork',
		'expires',
		'fileFormat',
		'funder',
		'genre',
		'hasPart',
		'headline',
		'inLanguage',
		'interactionStatistic',
		'interactivityType',
		'isAccessibleForFree',
		'isBasedOn',
		'isFamilyFriendly',
		'isPartOf',
		'keywords',
		'learningResourceType',
		'license',
		'locationCreated',
		'mainEntity',
		'material',
		'mentions',
		'offers',
		'position',
		'producer',
		'provider',
		'publication',
		'publisher',
		'publishingPrinciples',
		'recordedAt',
		'releasedEvent',
		'review',
		'schemaVersion',
		'sourceOrganization',
		'spatialCoverage',
		'sponsor',
		'temporalCoverage',
		'text',
		'thumbnailUrl',
		'timeRequired',
		'translator',
		'typicalAgeRange',
		'version',
		'video',
		'workExample',
	);

	/**
	 * Get the post author.
	 *
	 * @param int $post_id The post ID.
	 * @return string The post creation date.
	 */
	public function getAuthor( $post_id ) {
		if ( ! $this->isMain ) {
			return null;
		}

		$post = get_post( $post_id );

		return $post ? new WP\User( $post->post_author ) : null;
	}

	/**
	 * Get the post creation date.
	 *
	 * @param int $post_id The post ID.
	 * @return string The post creation date.
	 */
	public function getDateCreated( $post_id ) {
		return get_post_time( 'c', true, $post_id );
	}

	/**
	 * Get the post creation date.
	 *
	 * @param int $post_id The post ID.
	 * @return string The post creation date.
	 */
	public function getDateModified( $post_id ) {
		return get_post_modified_time( 'c', true, $post_id );
	}

	/**
	 * Get the post creation date.
	 *
	 * @param int $post_id The post ID.
	 * @return string The post creation date.
	 */
	public function getDatePublished( $post_id ) {
		return $this->getDateCreated( $post_id );
	}

	/**
	 * Get the headline for a post.
	 *
	 * @param int $post_id The post ID.
	 * @return string The post headline.
	 */
	public function getHeadline( $post_id ) {
		return $this->getName( $post_id );
	}

	/**
	 * Retrieve the work's publisher.
	 *
	 * @param int $post_id The post ID.
	 * @return Organization The publisher/organization.
	 */
	public function getPublisher( $post_id ) {
		return $this->isMain ? new WP\Organization( get_current_blog_id() ) : null;
	}

	/**
	 * Retrieve the thumbnail URL for a post.
	 *
	 * @param int $post_id The post ID.
	 * @return string The post's thumbnail URL.
	 */
	public function getThumbnailUrl( $post_id ) {
		$thumbnail = wp_get_attachment_image_src( get_post_thumbnail_id( $post_id ), 'thumbnail', true );

		return $thumbnail ? $thumbnail[0] : null;
	}
}
