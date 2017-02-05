<?php
/**
 * A dummy schema that (further) extends Thing.
 *
 * Note: The ThingTest::testGetPropertyListInheritsParentValues() method relies on these values,
 * to avoid manipulating a bunch of static, protected properties. Do not change $properties or
 * $removeProperties without updating the tests accordingly!
 */

namespace Schemify\Schemas;

class TestGrandchildSchema extends TestChildSchema {

	protected static $properties = array(
		'grandchildFoo',
		'grandchildBar',
	);

	protected static $removeProperties = array(
		'childBar',
		'childBaz',
	);
}
