<?php
/**
 * Plugin Name: Schemify
 * Plugin URI:  https://stevegrunwell.com/schemify
 * Description: Automatically generate Schema.org JSON-LD markup for WordPress.
 * Version:     0.1.0
 * Author:      Steve Grunwell
 * Author URI:  https://stevegrunwell.com
 * Text Domain: schemify
 *
 * @package Schemify
 */

namespace Schemify;

define( 'SCHEMIFY_VERSION', '0.1.0' );

require_once __DIR__ . '/includes/cache.php';
require_once __DIR__ . '/includes/schemas.php';
require_once __DIR__ . '/includes/core.php';
require_once __DIR__ . '/includes/admin.php';
require_once __DIR__ . '/includes/theme.php';
require_once __DIR__ . '/includes/compat.php';
