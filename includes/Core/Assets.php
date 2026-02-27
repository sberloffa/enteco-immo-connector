<?php
/**
 * Enqueues admin CSS and JS only on plugin-owned admin pages.
 */

declare( strict_types=1 );

namespace Enteco\ImmoConnector\Core;

class Assets {

	public function enqueue_admin_assets( string $hook_suffix ): void {
		$eic_pages = [ 'toplevel_page_eic_dashboard', 'immo-connector_page_eic_settings', 'immo-connector_page_eic_import' ];

		if ( ! in_array( $hook_suffix, $eic_pages, true ) ) {
			return;
		}

		wp_enqueue_style(
			'eic-admin',
			EIC_PLUGIN_URL . 'assets/css/admin.css',
			[],
			EIC_VERSION
		);

		wp_enqueue_script(
			'eic-admin',
			EIC_PLUGIN_URL . 'assets/js/admin.js',
			[ 'jquery' ],
			EIC_VERSION,
			true
		);

		wp_localize_script(
			'eic-admin',
			'eicAdmin',
			[
				'ajaxUrl' => admin_url( 'admin-ajax.php' ),
				'nonce'   => wp_create_nonce( 'eic_import_nonce' ),
				'i18n'    => [
					'importing' => __( 'Import läuft…', 'enteco-immo-connector' ),
					'success'   => __( 'Import erfolgreich.', 'enteco-immo-connector' ),
					'error'     => __( 'Import fehlgeschlagen.', 'enteco-immo-connector' ),
				],
			]
		);
	}
}
