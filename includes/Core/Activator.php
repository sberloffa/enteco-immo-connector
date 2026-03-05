<?php
/**
 * Plugin Activator – runs on plugin activation.
 *
 * @package Enteco\ImmoConnector\Core
 */

declare(strict_types=1);

namespace Enteco\ImmoConnector\Core;

/**
 * Handles plugin activation tasks.
 */
final class Activator {

	/** Run all activation tasks. */
	public static function activate(): void {
		self::check_requirements();
		self::set_default_options();
		self::flush_rewrite_rules();
	}

	/** Verify minimum requirements and abort with message if not met. */
	private static function check_requirements(): void {
		global $wp_version;

		if ( version_compare( PHP_VERSION, EIC_MIN_PHP, '<' ) ) {
			deactivate_plugins( EIC_PLUGIN_BASENAME );
			wp_die(
				esc_html(
					sprintf(
						/* translators: 1: Required PHP version */
						__( 'Enteco Immo Connector benötigt PHP %s oder höher.', 'enteco-immo-connector' ),
						EIC_MIN_PHP
					)
				)
			);
		}

		if ( version_compare( $wp_version, EIC_MIN_WP, '<' ) ) {
			deactivate_plugins( EIC_PLUGIN_BASENAME );
			wp_die(
				esc_html(
					sprintf(
						/* translators: 1: Required WP version */
						__( 'Enteco Immo Connector benötigt WordPress %s oder höher.', 'enteco-immo-connector' ),
						EIC_MIN_WP
					)
				)
			);
		}
	}

	/** Store initial option defaults (only if not already set). */
	private static function set_default_options(): void {
		$defaults = [
			'eic_field_engine'             => 'native',
			'eic_active_provider'          => '',
			'eic_delete_data_on_uninstall' => 'no',
			'eic_version'                  => EIC_VERSION,
			'eic_onboarding_complete'      => false,
		];

		foreach ( $defaults as $key => $value ) {
			if ( false === get_option( $key ) ) {
				update_option( $key, $value, false );
			}
		}
	}

	/** Schedule rewrite rules flush on next init. */
	private static function flush_rewrite_rules(): void {
		update_option( 'eic_flush_rewrite_rules', true, false );
	}
}
