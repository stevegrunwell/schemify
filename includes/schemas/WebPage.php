<?php
/**
 * The WebPage Schema.
 *
 * @package Schemify
 * @link    http://schema.org/WebPage
 */

namespace Schemify\Schemas;

class WebPage extends CreativeWork {

	/**
	 * The properties this schema may utilize.
	 *
	 * @var array $properties
	 */
	protected static $properties = array(
		'breadcrumb',
		'lastReviewed',
		'mainContentOfPage',
		'primaryImageOfPage',
		'relatedLink',
		'reviewedBy',
		'significantLink',
		'specialty',
	);
}
