<?php
/**
 * Plugin Name: ic Rewrite Control
 * Plugin URI:  https://github.com/inerciacreativa/wp-rewrite-control
 * Version:     1.0.0
 * Description: Gestor de .htaccess y opciones de WP_Rewrite.
 * Author:      Jose Cuesta
 * Author URI:  https://inerciacreativa.com/
 * Text Domain: ic-rewrite-control
 * Domain Path: /languages
 * License:     MIT
 * License URI: https://opensource.org/licenses/MIT
 */

if (!defined('ABSPATH')) {
	exit;
}

include_once __DIR__ . '/vendor/autoload.php';

ic\Plugin\RewriteControl\RewriteControl::create(__FILE__);
