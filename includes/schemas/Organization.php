<?php
/**
 * The Organization Schema.
 *
 * @package Schemify
 * @link    http://schema.org/Organization
 */

namespace Schemify\Schemas;

class Organization extends Thing {

	/**
	 * The properties this schema may utilize.
	 *
	 * @var array $properties
	 */
	protected static $properties = array(
		'address',
		'aggregateRating',
		'alumni',
		'areaServed',
		'award',
		'brand',
		'contactPoint',
		'department',
		'dissolutionDate',
		'duns',
		'email',
		'employee',
		'event',
		'faxNumber',
		'founder',
		'foundingDate',
		'foundingLocation',
		'globalLocationNumber',
		'hasOfferCatalog',
		'hasPOS',
		'isicV4',
		'legalName',
		'leiCode',
		'location',
		'logo',
		'makesOffer',
		'member',
		'memberOf',
		'naics',
		'numberOfEmployees',
		'owns',
		'parentOrganization',
		'review',
		'seeks',
		'sponsor',
		'subOrganization',
		'taxId',
		'telephone',
		'vatID',
	);
}
