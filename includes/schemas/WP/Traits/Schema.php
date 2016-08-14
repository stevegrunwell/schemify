<?php
/**
 * A trait to help conform the WP-specific schemas to the spec.
 *
 * @package Schemify
 */

namespace Schemify\Schemas\WP\Traits;

use Schemify\Core as Core;

trait Schema {

	/**
	 * Override the default getSchema() implementation from Thing, explicitly setting $this->schema
	 * so the default implementation will pull that instead.
	 *
	 * This lets us avoid the @type key being something like "Blog", which is not a valid schema.
	 */
	public function getSchema() {
		if ( ! $this->schema ) {
			$this->schema = Core\strip_namespace( get_parent_class( $this ) );
		}

		return parent::getSchema();
	}
}
