<?php
/**
 * Tests for the core functionality.
 *
 * @package Schemify
 */

namespace Test;

use Schemify\Admin as Admin;
use Schemify\Core as Core;
use WP_UnitTestCase;

class AdminTest extends WP_UnitTestCase {

	public function testRegistersMetaBoxes() {
		global $wp_meta_boxes;

		do_action( 'add_meta_boxes' );

		$this->assertArrayHasKey( 'schemify', $wp_meta_boxes['post']['advanced']['default'] );
	}

	public function testRendersMetaBoxes() {
		$post = $this->factory()->post->create_and_get();
		$json = wp_json_encode( Core\build_object( $post->ID ), JSON_PRETTY_PRINT );

		ob_start();
		Admin\meta_box_cb( $post );
		$output = ob_get_clean();

		$this->assertContains( $json, $output );
	}
}
