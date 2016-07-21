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
}
