<?php
/**
 * Loads the plugin text domain for translations.
 */

declare( strict_types=1 );

namespace Enteco\ImmoConnector\Core;

class I18n {

	public function load_plugin_textdomain(): void {
		load_plugin_textdomain(
			'enteco-immo-connector',
			false,
			dirname( EIC_PLUGIN_BASENAME ) . '/languages/'
		);
	}
}
