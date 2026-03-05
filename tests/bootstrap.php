<?php
/**
 * PHPUnit bootstrap – loads autoloader and Brain\Monkey stubs.
 *
 * @package Enteco\ImmoConnector\Tests
 */

declare(strict_types=1);

require_once dirname(__DIR__) . '/vendor/autoload.php';

// Define plugin constants so classes don't fatally error when loaded.
if (!defined('EIC_VERSION')) {
    define('EIC_VERSION', '1.0.0-test');
}
if (!defined('EIC_PLUGIN_FILE')) {
    define('EIC_PLUGIN_FILE', dirname(__DIR__) . '/enteco-immo-connector.php');
}
if (!defined('EIC_PLUGIN_DIR')) {
    define('EIC_PLUGIN_DIR', dirname(__DIR__) . '/');
}
if (!defined('EIC_PLUGIN_URL')) {
    define('EIC_PLUGIN_URL', 'http://localhost/wp-content/plugins/enteco-immo-connector/');
}
if (!defined('EIC_PLUGIN_BASENAME')) {
    define('EIC_PLUGIN_BASENAME', 'enteco-immo-connector/enteco-immo-connector.php');
}
if (!defined('EIC_MIN_PHP')) {
    define('EIC_MIN_PHP', '8.1');
}
if (!defined('EIC_MIN_WP')) {
    define('EIC_MIN_WP', '6.0');
}
if (!defined('ABSPATH')) {
    define('ABSPATH', '/var/www/html/');
}
if (!defined('HOUR_IN_SECONDS')) {
    define('HOUR_IN_SECONDS', 3600);
}
if (!defined('WEEK_IN_SECONDS')) {
    define('WEEK_IN_SECONDS', 604800);
}
if (!defined('LIBXML_NONET')) {
    define('LIBXML_NONET', 2048);
}
