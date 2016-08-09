<?php
/**
 * The Article Schema.
 *
 * @package Schemify
 * @link    http://schema.org/Article
 */

namespace Schemify\Schemas;

class Article extends CreativeWork {

	/**
	 * The properties this schema may utilize.
	 *
	 * @var array $properties
	 */
	protected static $properties = array(
		'articleBody',
		'articleSection',
		'pageEnd',
		'pageStart',
		'pagination',
		'wordCount',
	);

	/**
	 * Retrieve the word-count of the post's post_content.
	 *
	 * @link http://www.thomashardy.me.uk/wordpress-word-count-function
	 *
	 * @param int $post_id The post ID.
	 * @return int The number of words in the post.
	 */
	public function getWordCount( $post_id ) {
		$content = get_post_field( 'post_content', $post_id );

		return str_word_count( strip_tags( $content ) );
	}
}
