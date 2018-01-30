<?php
/**
 * The SearchResultsPage Schema.
 *
 * @package Schemify
 * @link    http://schema.org/SearchResultsPage
 */

namespace Schemify\Schemas;

class SearchResultsPage extends WebPage {

	/**
	 * Properties that would have normally been inherited but should not exist in a sub-tree.
	 *
	 * @var array $removeProperties
	 */
	protected static $removeProperties = array(
		'author',
		'dateCreated',
		'dateModified',
		'datePublished',
		'headline',
		'image',
		'thumbnailUrl',
	);

	/**
	 * Instead of showing the description from the first post in the search results, set it so it's
	 * clear that it's a search results page.
	 *
	 * @global $wp_query
	 *
	 * @param int $post_id The attachment ID.
	 * @return string The content URL.
	 */
	public function getDescription( $post_id ) {
		global $wp_query;

		return sprintf(
			/* Translators: %1$d is the number of results, %2$s the query, and %3$s the blog name. */
			_n(
				'%1$d search result found for "%2$s" on %3$s',
				'%1$d search results found for "%2$s" on %3$s',
				$wp_query->found_posts,
				'schemify'
			),
			$wp_query->found_posts,
			get_search_query(),
			get_option( 'blogname' )
		);
	}

	/**
	 * Instead of showing the description from the first post in the search results, set it so it's
	 * clear that it's a search results page.
	 *
	 * @param int $post_id The attachment ID.
	 * @return string The content URL.
	 */
	public function getName( $post_id ) {
		/* Translators: %1$s is the search query. */
		return sprintf( __( 'Search results for "%1$s"', 'schemify' ), get_search_query() );
	}
}
