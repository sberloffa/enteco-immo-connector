<?php
/**
 * Runs on plugin activation: registers CPTs and flushes rewrite rules.
 */

declare( strict_types=1 );

namespace Enteco\ImmoConnector\Core;

use Enteco\ImmoConnector\PostTypes\AgentPostType;
use Enteco\ImmoConnector\PostTypes\PropertyPostType;

class Activator {

	public static function activate(): void {
		( new PropertyPostType() )->register();
		( new AgentPostType() )->register();
		flush_rewrite_rules();

		// Schedule daily automatic import (only if mode is automatic or not yet set).
		if ( ! wp_next_scheduled( 'eic_daily_import' ) ) {
			wp_schedule_event( time(), 'daily', 'eic_daily_import' );
		}
	}
}
