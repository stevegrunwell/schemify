<?php
/**
 * The Person Schema.
 *
 * @package Schemify
 * @link    http://schema.org/Person
 */

namespace Schemify\Schemas;

class Person extends Thing {

	/**
	 * The properties this schema may utilize.
	 *
	 * @var array $properties
	 */
	protected static $properties = array(
		'additionalName',
		'address',
		'affiliation',
		'alumniOf',
		'award',
		'birthDate',
		'birthPlace',
		'brand',
		'children',
		'colleague',
		'contactPoint',
		'deathDate',
		'deathPlace',
		'duns',
		'email',
		'familyName',
		'faxNumber',
		'follows',
		'funder',
		'gender',
		'givenName',
		'globalLocationNumber',
		'hasOfferCatalog',
		'hasPOS',
		'height',
		'homeLocation',
		'honorificPrefix',
		'honorificSuffix',
		'isicV4',
		'jobTitle',
		'knows',
		'makesOffer',
		'memberOf',
		'naics',
		'nationality',
		'netWorth',
		'owns',
		'parent',
		'performerIn',
		'publishingPrinciples',
		'relatedTo',
		'seeks',
		'sibling',
		'sponsor',
		'spouse',
		'taxID',
		'telephone',
		'vatID',
		'weight',
		'workLocation',
		'worksFor',
	);
}
