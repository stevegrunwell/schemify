<?php
/**
 * Base test case for Schemify.
 *
 * @package Schemify
 */

namespace Schemify;

class TestCase extends \WP_Mock\Tools\TestCase {

	protected $testFiles = array();

	public function setUp() {
		if ( ! empty( $this->testFiles ) ) {
			foreach ( $this->testFiles as $file ) {
				if ( file_exists( PROJECT . $file ) ) {
					require_once( PROJECT . $file );
				}
			}
		}
		parent::setUp();
	}

	public function run( \PHPUnit_Framework_TestResult $result = null ) {
		$this->setPreserveGlobalState( false );
		return parent::run( $result );
	}

	public function assertActionsCalled() {
		$actions_not_added = $expected_actions = 0;
		try {
			WP_Mock::assertActionsCalled();
		} catch ( \Exception $e ) {
			$actions_not_added = 1;
			$expected_actions  = $e->getMessage();
		}
		$this->assertEmpty( $actions_not_added, $expected_actions );
	}

	/**
	 * Define constants after requires/includes
	 *
	 * See http://kpayne.me/2012/07/02/phpunit-process-isolation-and-constant-already-defined/
	 * for more details
	 *
	 * @param \Text_Template $template
	 */
	public function prepareTemplate( \Text_Template $template ) {
		$template->setVar( [
			'globals' => '$GLOBALS[\'__PHPUNIT_BOOTSTRAP\'] = \'' . $GLOBALS['__PHPUNIT_BOOTSTRAP'] . '\';',
		] );
		parent::prepareTemplate( $template );
	}
}
