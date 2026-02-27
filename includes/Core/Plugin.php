<?php
/**
 * Central hook registration and service wiring.
 */

declare( strict_types=1 );

namespace Enteco\ImmoConnector\Core;

use Enteco\ImmoConnector\Admin\AdminPage;
use Enteco\ImmoConnector\Import\ImportEngine;
use Enteco\ImmoConnector\PostTypes\AgentPostType;
use Enteco\ImmoConnector\PostTypes\PropertyPostType;
use Enteco\ImmoConnector\Taxonomies\PropertyTaxonomies;

class Plugin {

	public function run(): void {
		$i18n = new I18n();
		add_action( 'plugins_loaded', [ $i18n, 'load_plugin_textdomain' ] );

		$assets = new Assets();
		add_action( 'admin_enqueue_scripts', [ $assets, 'enqueue_admin_assets' ] );

		$property_cpt = new PropertyPostType();
		add_action( 'init', [ $property_cpt, 'register' ] );

		$agent_cpt = new AgentPostType();
		add_action( 'init', [ $agent_cpt, 'register' ] );

		$taxonomies = new PropertyTaxonomies();
		add_action( 'init', [ $taxonomies, 'register' ] );

		$admin = new AdminPage();
		add_action( 'admin_menu', [ $admin, 'register_menus' ] );

		// AJAX handler for manual import.
		add_action( 'wp_ajax_eic_run_import', [ $this, 'handle_import_ajax' ] );
	}

	public function handle_import_ajax(): void {
		check_ajax_referer( 'eic_import_nonce', 'nonce' );

		if ( ! current_user_can( 'manage_options' ) ) {
			wp_send_json_error(
				[ 'message' => __( 'Unzureichende Berechtigungen.', 'enteco-immo-connector' ) ],
				403
			);
		}

		$engine = new ImportEngine();
		$job    = $engine->run();

		if ( $job->is_success() ) {
			wp_send_json_success( $job->to_array() );
		} else {
			wp_send_json_error( $job->to_array() );
		}
	}
}
