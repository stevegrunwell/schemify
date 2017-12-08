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
		'audience',
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
		'maximumAttendeeCapacity',
		'offers',
		'organizer',
		'performer',
		'previousStartDate',
		'recordedIn',
		'remainingAttendeeCapacity',
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
