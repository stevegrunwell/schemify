<?php
/**
 * A representation of a WordPress Blog as a WebSite.
 *
 * @package Schemify
 * @link    http://schema.org/WebSite
 */

namespace Schemify\Schemas\WP;

use Schemify\Schemas as Schemas;

class WebSite extends Schemas\WebSite {

	use Traits\Schema, Traits\Blog;

	/**
	 * Remove the "author" node for WebSite.
	 *
	 * @param int $blog_id The blog ID. This value isn't used, but is preserved for compatibility
	 *                     with CreativeWork::getAuthor().
	 */
	public function getAuthor( $blog_id ) {
		return null;
	}

	/**
	 * Remove the "publisher" node for WebSite.
	 *
	 * @param int $blog_id The blog ID. This value isn't used, but is preserved for compatibility
	 *                     with CreativeWork::getPublisher().
	 */
	public function getPublisher( $blog_id ) {
		return null;
	}

	/**
	 * Remove the "thumbnailUrl" node for WebSite.
	 *
	 * @param int $blog_id The blog ID. This value isn't used, but is preserved for compatibility
	 *                     with CreativeWork::getPublisher().
	 */
	public function getThumbnailUrl( $blog_id ) {
		return null;
	}
}
