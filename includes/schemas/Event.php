<?php
/**
 * The Event Schema.
 *
 * @package Schemify
 * @link    http://schema.org/Event
 */

namespace Schemify\Schemas;

class Event extends Thing {

	/**
	 * The properties this schema may utilize.
	 *
	 * @var array $properties
	 */
	protected static $properties = array(
		'actor',
		'aggregateRating',
		'attendee',
		'composer',
		'contributor',
		'director',
		'doorTime',
		'duration',
		'endDate',
		'eventStatus',
		'funder',
		'inLanguage',
		'isAccessibleForFree',
		'location',
		'offers',
		'organizer',
		'performer',
		'previousStartDate',
		'recordedIn',
		'review',
		'sponsor',
		'startDate',
		'subEvent',
		'superEvent',
		'translator',
		'typicalAgeRange',
		'workFeatured',
		'workPerformed',
	);
}
