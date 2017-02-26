<?php
/**
 * Tests for The Events Calendar compatibility functions.
 *
 * @package Schemify
 */

namespace Schemify\Compat\TheEventsCalendar;

use WP_Mock as M;
use Schemify;

class TheEventsCalendarTest extends Schemify\TestCase {

	protected $testFiles = array(
		'compat/the-events-calendar.php',
	);

	public function testEnableSchemify() {
		M::userFunction( 'add_post_type_support', array(
			'times' => 1,
			'args'  => array( 'tribe_events', 'schemify' ),
		) );

		enable_schemify();
	}

	public function testMergeSchemaData() {
		$tec_schema              = new \stdClass;
		$tec_schema->name        = 'Event title';
		$tec_schema->description = 'Event description';
		$our_schema              = new \stdClass;
		$our_schema->name        = 'Overridden';
		$our_schema->url         = 'https://example.com';
		$event                   = new \stdClass;
		$event->ID               = 123;

		M::userFunction( 'Schemify\Core\build_object', array(
			'return' => $our_schema,
		) );

		$merged = merge_schema_data( $tec_schema, array(), $event );

		$this->assertEquals( 'Event title', $merged->name );
		$this->assertEquals( 'Event description', $merged->description );
		$this->assertEquals( 'https://example.com', $merged->url );
	}
}
