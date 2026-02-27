<?php
/**
 * Manual import trigger page with last-run status display.
 */

declare( strict_types=1 );

namespace Enteco\ImmoConnector\Admin;

class ImportStatusPage {

	public function render(): void {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( esc_html__( 'Unzureichende Berechtigungen.', 'enteco-immo-connector' ) );
		}

		$provider = (string) get_option( 'eic_provider', '' );

		require EIC_PLUGIN_DIR . 'includes/Admin/views/import-status.php';
	}
}
