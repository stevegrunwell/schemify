<?php
/**
 * Tests for the plugin's core functionality.
 *
 * @package Schemify
 */

namespace Schemify\Compat;

use WP_Mock as M;
use Schemify;

class CompatTest extends Schemify\TestCase {

	protected $testFiles = array(
		'compat.php',
	);

	public function testLoadCompatFiles() {
		$this->markTestIncomplete( 'Should verify that compatibility files exist before loading.' );
	}
}
