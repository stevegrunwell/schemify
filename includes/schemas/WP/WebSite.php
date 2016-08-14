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

	use WP_Schema, WP_Blog;
}
