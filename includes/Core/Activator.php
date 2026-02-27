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
	}
}
