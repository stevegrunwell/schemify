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
	 * Properties that would have normally been inherited but should not exist in a sub-tree.
	 *
	 * @var array $removeProperties
	 */
	protected static $removeProperties = array(
		'author',
		'publisher',
		'thumbnailUrl',
	);
}
