<?php
/**
 * Registers the admin menu tree and dispatches to sub-pages.
 */

declare( strict_types=1 );

namespace Enteco\ImmoConnector\Admin;

class AdminPage {

	public function register_menus(): void {
		add_menu_page(
			__( 'Immo Connector', 'enteco-immo-connector' ),
			__( 'Immo Connector', 'enteco-immo-connector' ),
			'manage_options',
			'eic_dashboard',
			[ $this, 'render_dashboard' ],
			'dashicons-building',
			30
		);

		add_submenu_page(
			'eic_dashboard',
			__( 'Übersicht', 'enteco-immo-connector' ),
			__( 'Übersicht', 'enteco-immo-connector' ),
			'manage_options',
			'eic_dashboard',
			[ $this, 'render_dashboard' ]
		);

		add_submenu_page(
			'eic_dashboard',
			__( 'Einstellungen', 'enteco-immo-connector' ),
			__( 'Einstellungen', 'enteco-immo-connector' ),
			'manage_options',
			'eic_settings',
			[ new SettingsPage(), 'render' ]
		);

		add_submenu_page(
			'eic_dashboard',
			__( 'Import', 'enteco-immo-connector' ),
			__( 'Import', 'enteco-immo-connector' ),
			'manage_options',
			'eic_import',
			[ new ImportStatusPage(), 'render' ]
		);

		add_submenu_page(
			'eic_dashboard',
			__( 'Immobilien', 'enteco-immo-connector' ),
			__( 'Immobilien', 'enteco-immo-connector' ),
			'manage_options',
			'edit.php?post_type=eic_property'
		);

		add_submenu_page(
			'eic_dashboard',
			__( 'Makler', 'enteco-immo-connector' ),
			__( 'Makler', 'enteco-immo-connector' ),
			'manage_options',
			'edit.php?post_type=eic_agent'
		);
	}

	public function render_dashboard(): void {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( esc_html__( 'Unzureichende Berechtigungen.', 'enteco-immo-connector' ) );
		}

		// Show onboarding wizard on first run.
		if ( ! get_option( 'eic_field_engine' ) ) {
			( new OnboardingWizard() )->render();
			return;
		}

		require EIC_PLUGIN_DIR . 'includes/Admin/views/dashboard.php';
	}
}
