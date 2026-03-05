<?php
/**
 * Plugin Name:       Enteco Immo Connector
 * Plugin URI:        https://enteco.at/immo-connector
 * Description:       Importiert Immobilien-Daten aus Justimmo und OnOffice in WordPress (OpenImmo 1.2.7c).
 * Version:           1.0.0
 * Author:            Enteco
 * Author URI:        https://enteco.at
 * License:           GPL-2.0-or-later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       enteco-immo-connector
 * Domain Path:       /languages
 * Requires at least: 6.0
 * Requires PHP:      8.1
 * Tested up to:      6.7
 *
 * @package Enteco\ImmoConnector
 */

declare(strict_types=1);

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Plugin constants.
define( 'EIC_VERSION', '1.0.0' );
define( 'EIC_PLUGIN_FILE', __FILE__ );
define( 'EIC_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'EIC_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
define( 'EIC_PLUGIN_BASENAME', plugin_basename( __FILE__ ) );
define( 'EIC_MIN_PHP', '8.1' );
define( 'EIC_MIN_WP', '6.0' );

// PHP version check before autoloading.
if ( version_compare( PHP_VERSION, EIC_MIN_PHP, '<' ) ) {
	add_action(
		'admin_notices',
		function () {
			printf(
				'<div class="notice notice-error"><p>%s</p></div>',
				esc_html(
					sprintf(
						/* translators: 1: Required PHP version, 2: Current PHP version */
						__( 'Enteco Immo Connector benötigt PHP %1$s oder höher. Aktuelle Version: %2$s.', 'enteco-immo-connector' ),
						EIC_MIN_PHP,
						PHP_VERSION
					)
				)
			);
		}
	);
	return;
}

// Autoloader (Composer or manual fallback).
if ( file_exists( EIC_PLUGIN_DIR . 'vendor/autoload.php' ) ) {
	require_once EIC_PLUGIN_DIR . 'vendor/autoload.php';
} else {
	require_once EIC_PLUGIN_DIR . 'includes/autoload.php';
}

use Enteco\ImmoConnector\Core\Plugin;
use Enteco\ImmoConnector\Core\Activator;
use Enteco\ImmoConnector\Core\Deactivator;

// Activation / deactivation hooks (must be registered before Plugin::get_instance()).
register_activation_hook( EIC_PLUGIN_FILE, [ Activator::class, 'activate' ] );
register_deactivation_hook( EIC_PLUGIN_FILE, [ Deactivator::class, 'deactivate' ] );

// Boot the plugin.
Plugin::get_instance()->run();
