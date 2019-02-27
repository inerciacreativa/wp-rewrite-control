<?php
/**
 * Plugin Name: ic Rewrite Control
 * Plugin URI:  https://github.com/inerciacreativa/wp-rewrite-control
 * Version:     2.0.10
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

if (!class_exists(ic\Framework\Framework::class)) {
	trigger_error('ic Framework not found', E_USER_ERROR);
}

if (!class_exists(ic\Plugin\RewriteControl\RewriteControl::class)) {
	if (file_exists(__DIR__ . '/vendor/autoload.php')) {
		include_once __DIR__ . '/vendor/autoload.php';
	} else {
		trigger_error('Could not load RewriteControl class', E_USER_ERROR);
	}
}

ic\Plugin\RewriteControl\RewriteControl::create(__FILE__);
