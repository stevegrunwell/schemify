<?php

namespace Schemify\Schemas;

class TestSchema {

	protected $schema;

	public function __construct( $post_id, $is_main = false ) {
		// Do nothing.
	}

	public static function getProperties() {
		return array(
			'name' => 'Test Object',
		);
	}

	public function getSchema() {
		return $this->schema;
	}
}