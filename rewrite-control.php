<?php
/**
 * Plugin Name: ic Rewrite Control
 * Plugin URI:  https://github.com/inerciacreativa/wp-rewrite-control
 * Version:     4.0.2
 * Text Domain: ic-rewrite-control
 * Domain Path: /languages
 * Description: Gestor de .htaccess y opciones de WP_Rewrite.
 * Author:      Jose Cuesta
 * Author URI:  https://inerciacreativa.com/
 * License:     MIT
 * License URI: https://opensource.org/licenses/MIT
 */

use ic\Framework\Framework;
use ic\Plugin\RewriteControl\RewriteControl;

if (!defined('ABSPATH')) {
	exit;
}

if (!class_exists(Framework::class)) {
	throw new RuntimeException(sprintf('Could not find %s class.', Framework::class));
}

if (!class_exists(RewriteControl::class)) {
	$autoload = __DIR__ . '/vendor/autoload.php';

	if (file_exists($autoload)) {
		/** @noinspection PhpIncludeInspection */
		include_once $autoload;
	} else {
		throw new RuntimeException(sprintf('Could not load %s class.', RewriteControl::class));
	}
}

RewriteControl::create(__FILE__);
