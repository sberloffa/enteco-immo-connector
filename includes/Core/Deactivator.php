<?php
/**
 * Plugin Deactivator – runs on plugin deactivation.
 *
 * @package Enteco\ImmoConnector\Core
 */

declare(strict_types=1);

namespace Enteco\ImmoConnector\Core;

/**
 * Handles plugin deactivation tasks.
 */
final class Deactivator {

	/** Run all deactivation tasks. */
	public static function deactivate(): void {
		self::clear_scheduled_events();
		flush_rewrite_rules();
	}

	/** Remove any WP-Cron events registered by this plugin. */
	private static function clear_scheduled_events(): void {
		$hooks = [
			'eic_scheduled_import',
		];

		foreach ( $hooks as $hook ) {
			$timestamp = wp_next_scheduled( $hook );
			if ( $timestamp ) {
				wp_unschedule_event( $timestamp, $hook );
			}
		}
	}
}
