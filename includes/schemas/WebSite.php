<?php
/**
 * The WebSite Schema.
 *
 * @package Schemify
 * @link    http://schema.org/WebSite
 */

namespace Schemify\Schemas;

class WebSite extends CreativeWork {

	/**
	 * The properties this schema may utilize.
	 *
	 * @var array $properties
	 */
	protected static $properties = array(
		'issn',
	);

	/**
	 * Retrieve the website's author.
	 *
	 * Since a website also has the publisher property, use that instead of calling out a specific
	 * author object.
	 *
	 * @param int $post_id The post ID.
	 * @return null
	 */
	public function getAuthor( $post_id ) {
		return null;
	}
}
