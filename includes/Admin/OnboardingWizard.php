<?php
/**
 * First-run wizard: lets the user confirm the Field Engine.
 * FREE version: only the Native engine is available.
 */

declare( strict_types=1 );

namespace Enteco\ImmoConnector\Admin;

class OnboardingWizard {

	public function render(): void {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( esc_html__( 'Unzureichende Berechtigungen.', 'enteco-immo-connector' ) );
		}

		if ( isset( $_POST['eic_onboarding_nonce'] ) ) {
			$this->handle_submit();
		}

		require EIC_PLUGIN_DIR . 'includes/Admin/views/onboarding.php';
	}

	private function handle_submit(): void {
		if ( ! wp_verify_nonce(
			sanitize_key( wp_unslash( $_POST['eic_onboarding_nonce'] ) ),
			'eic_onboarding'
		) ) {
			wp_die( esc_html__( 'Sicherheitsüberprüfung fehlgeschlagen.', 'enteco-immo-connector' ) );
		}

		// FREE: always native — hard-coded to prevent engine switching after setup.
		update_option( 'eic_field_engine', 'native' );

		wp_safe_redirect(
			add_query_arg( 'page', 'eic_settings', admin_url( 'admin.php' ) )
		);
		exit;
	}
}
