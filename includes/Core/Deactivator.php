<?php
/**
 * Runs on plugin deactivation: flushes rules and clears import lock.
 */

declare( strict_types=1 );

namespace Enteco\ImmoConnector\Core;

class Deactivator {

	public static function deactivate(): void {
		flush_rewrite_rules();
		delete_transient( 'eic_import_lock' );
		delete_option( 'eic_import_lock_time' );

		$timestamp = wp_next_scheduled( 'eic_daily_import' );
		if ( $timestamp ) {
			wp_unschedule_event( $timestamp, 'eic_daily_import' );
		}
	}
}
