<?php
/**
 * Plugin Name: ic Rewrite Control
 * Plugin URI:  https://github.com/inerciacreativa/wp-rewrite-control
 * Version:     2.0.2
 * Text Domain: ic-rewrite-control
 * Domain Path: /languages
 * Description: Gestor de .htaccess y opciones de WP_Rewrite.
 * Author:      Jose Cuesta
 * Author URI:  https://inerciacreativa.com/
 * License:     MIT
 * License URI: https://opensource.org/licenses/MIT
 */

if (!defined('ABSPATH')) {
	exit;
}

include_once __DIR__ . '/vendor/autoload.php';

ic\Plugin\RewriteControl\RewriteControl::create(__FILE__);
