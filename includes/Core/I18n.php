<?php
/**
 * Internationalization loader.
 *
 * @package Enteco\ImmoConnector\Core
 */

declare(strict_types=1);

namespace Enteco\ImmoConnector\Core;

/**
 * Loads the plugin text domain.
 */
final class I18n {

	/** Load the plugin text domain. */
	public function load(): void {
		add_action( 'init', [ $this, 'load_textdomain' ] );
	}

	/** load_plugin_textdomain callback. */
	public function load_textdomain(): void {
		load_plugin_textdomain(
			'enteco-immo-connector',
			false,
			dirname( EIC_PLUGIN_BASENAME ) . '/languages'
		);
	}
}
