<?php
/**
 * Provider credentials and plugin settings page.
 */

declare( strict_types=1 );

namespace Enteco\ImmoConnector\Admin;

class SettingsPage {

	public function render(): void {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( esc_html__( 'Unzureichende Berechtigungen.', 'enteco-immo-connector' ) );
		}

		$this->handle_save();

		$provider            = (string) get_option( 'eic_provider', '' );
		$j_username          = (string) get_option( 'eic_justimmo_username', '' );
		$j_has_password      = ! empty( get_option( 'eic_justimmo_password', '' ) );
		$oo_token            = (string) get_option( 'eic_onoffice_token', '' );
		$oo_has_secret       = ! empty( get_option( 'eic_onoffice_secret', '' ) );
		$field_engine        = (string) get_option( 'eic_field_engine', 'native' );
		$import_mode         = (string) get_option( 'eic_import_mode', 'automatic' );
		$delete_on_uninstall = (string) get_option( 'eic_delete_data_on_uninstall', 'no' );

		require EIC_PLUGIN_DIR . 'includes/Admin/views/settings.php';
	}

	private function handle_save(): void {
		if ( ! isset( $_POST['eic_settings_nonce'] ) ) {
			return;
		}

		if ( ! wp_verify_nonce( sanitize_key( wp_unslash( $_POST['eic_settings_nonce'] ) ), 'eic_save_settings' ) ) {
			add_settings_error(
				'eic_settings',
				'invalid_nonce',
				__( 'Sicherheitsüberprüfung fehlgeschlagen.', 'enteco-immo-connector' )
			);
			return;
		}

		$provider = sanitize_key( wp_unslash( $_POST['eic_provider'] ?? '' ) );
		if ( in_array( $provider, [ 'justimmo', 'onoffice' ], true ) ) {
			update_option( 'eic_provider', $provider );
		}

		update_option(
			'eic_justimmo_username',
			sanitize_text_field( wp_unslash( $_POST['eic_justimmo_username'] ?? '' ) )
		);

		$new_password = wp_unslash( $_POST['eic_justimmo_password'] ?? '' );
		if ( ! empty( $new_password ) ) {
			update_option( 'eic_justimmo_password', sanitize_text_field( $new_password ) );
		}

		update_option(
			'eic_onoffice_token',
			sanitize_text_field( wp_unslash( $_POST['eic_onoffice_token'] ?? '' ) )
		);

		$new_secret = wp_unslash( $_POST['eic_onoffice_secret'] ?? '' );
		if ( ! empty( $new_secret ) ) {
			update_option( 'eic_onoffice_secret', sanitize_text_field( $new_secret ) );
		}

		$import_mode = sanitize_key( wp_unslash( $_POST['eic_import_mode'] ?? 'automatic' ) );
		if ( ! in_array( $import_mode, [ 'automatic', 'manual' ], true ) ) {
			$import_mode = 'automatic';
		}
		$old_import_mode = (string) get_option( 'eic_import_mode', 'automatic' );
		update_option( 'eic_import_mode', $import_mode );

		// Reschedule or unschedule cron when mode changes.
		if ( $import_mode !== $old_import_mode ) {
			if ( $import_mode === 'automatic' ) {
				if ( ! wp_next_scheduled( 'eic_daily_import' ) ) {
					wp_schedule_event( time(), 'daily', 'eic_daily_import' );
				}
			} else {
				$ts = wp_next_scheduled( 'eic_daily_import' );
				if ( $ts ) {
					wp_unschedule_event( $ts, 'eic_daily_import' );
				}
			}
		}

		$delete_flag = sanitize_key( wp_unslash( $_POST['eic_delete_data_on_uninstall'] ?? 'no' ) );
		update_option( 'eic_delete_data_on_uninstall', $delete_flag === 'yes' ? 'yes' : 'no' );

		add_settings_error(
			'eic_settings',
			'saved',
			__( 'Einstellungen gespeichert.', 'enteco-immo-connector' ),
			'updated'
		);
	}
}
