<?php
/**
 * The SocialMediaPosting Schema.
 *
 * @package Schemify
 * @link    http://schema.org/SocialMediaPosting
 */

namespace Schemify\Schemas;

class SocialMediaPosting extends Article {

	/**
	 * The properties this schema may utilize.
	 *
	 * @var array $properties
	 */
	protected static $properties = array(
		'sharedContent',
	);
}
