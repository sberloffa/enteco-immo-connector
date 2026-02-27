<?php
/**
 * Plugin Name:       Enteco Immo Connector
 * Plugin URI:        https://enteco.de/immo-connector
 * Description:       Import von Immobiliendaten aus Justimmo und OnOffice nach WordPress. Internes Datenmodell: OpenImmo 1.2.7c.
 * Version:           1.0.0
 * Requires at least: 6.0
 * Tested up to:      6.7
 * Requires PHP:      8.1
 * Author:            Enteco GmbH
 * Author URI:        https://enteco.de
 * License:           GPL-2.0-or-later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       enteco-immo-connector
 * Domain Path:       /languages
 */

declare( strict_types=1 );

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'EIC_VERSION', '1.0.0' );
define( 'EIC_PLUGIN_FILE', __FILE__ );
define( 'EIC_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'EIC_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
define( 'EIC_PLUGIN_BASENAME', plugin_basename( __FILE__ ) );

if ( file_exists( EIC_PLUGIN_DIR . 'vendor/autoload.php' ) ) {
	require_once EIC_PLUGIN_DIR . 'vendor/autoload.php';
}

use Enteco\ImmoConnector\Core\Activator;
use Enteco\ImmoConnector\Core\Deactivator;
use Enteco\ImmoConnector\Core\Plugin;

register_activation_hook( __FILE__, [ Activator::class, 'activate' ] );
register_deactivation_hook( __FILE__, [ Deactivator::class, 'deactivate' ] );

function eic_run(): void {
	$plugin = new Plugin();
	$plugin->run();
}

eic_run();
