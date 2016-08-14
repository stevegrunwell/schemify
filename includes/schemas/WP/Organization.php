<?php
/**
 * A representation of a WordPress Blog as an Organization.
 *
 * @package Schemify
 * @link    http://schema.org/Organization
 */

namespace Schemify\Schemas\WP;

use Schemify\Schemas as Schemas;

class Organization extends Schemas\Organization {

	use Traits\Schema, Traits\Blog;
}
