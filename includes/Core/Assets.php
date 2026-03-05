<?php
/**
 * Enqueue admin scripts and styles.
 *
 * @package Enteco\ImmoConnector\Core
 */

declare(strict_types=1);

namespace Enteco\ImmoConnector\Core;

/**
 * Manages admin asset enqueueing.
 */
final class Assets {

	/** Register hooks. */
	public function register(): void {
		add_action( 'admin_enqueue_scripts', [ $this, 'enqueue_admin_assets' ] );
	}

	/**
	 * Enqueue CSS and JS for EIC admin pages.
	 *
	 * @param string $hook_suffix The current admin page.
	 */
	public function enqueue_admin_assets( string $hook_suffix ): void {
		if ( ! $this->is_eic_page( $hook_suffix ) ) {
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
			[],
			EIC_VERSION,
			true
		);

		wp_localize_script(
			'eic-admin',
			'eicAdmin',
			[
				'nonce'  => wp_create_nonce( 'eic_admin_nonce' ),
				'ajaxUrl' => admin_url( 'admin-ajax.php' ),
				'i18n'   => [
					'importing'  => esc_html__( 'Import läuft…', 'enteco-immo-connector' ),
					'importDone' => esc_html__( 'Import abgeschlossen.', 'enteco-immo-connector' ),
					'importFail' => esc_html__( 'Import fehlgeschlagen.', 'enteco-immo-connector' ),
				],
			]
		);
	}

	/**
	 * Check if we're on an EIC admin page.
	 *
	 * @param string $hook_suffix Current page hook suffix.
	 */
	private function is_eic_page( string $hook_suffix ): bool {
		$eic_pages = [
			'toplevel_page_eic-dashboard',
			'immo-connector_page_eic-settings',
			'immo-connector_page_eic-import-status',
			'immo-connector_page_eic-license',
		];
		return in_array( $hook_suffix, $eic_pages, true );
	}
}
