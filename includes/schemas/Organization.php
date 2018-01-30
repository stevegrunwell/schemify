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
		'funder',
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
		'publishingPrinciples',
		'review',
		'seeks',
		'sponsor',
		'subOrganization',
		'taxID',
		'telephone',
		'vatID',
	);

	/**
	 * Get the organization's logo.
	 *
	 * For now, this will just default to the object's getImage() method.
	 *
	 * @param int $post_id The post ID.
	 * @return string The organization's logo.
	 */
	public function getLogo( $post_id ) {
		return $this->getImage( $post_id );
	}
}
