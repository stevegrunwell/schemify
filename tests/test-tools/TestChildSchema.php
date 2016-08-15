<?php
/**
 * A dummy schema that extends Thing.
 */

namespace Schemify\Schemas;

class TestChildSchema extends Thing {

	protected static $properties = array(
		'foo',
		'bar',
		'fooBar',
	);
}