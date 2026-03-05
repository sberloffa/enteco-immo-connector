<?php
/**
 * PSR-4 Autoloader fallback (used when Composer vendor dir is absent).
 *
 * @package Enteco\ImmoConnector
 */

declare(strict_types=1);

spl_autoload_register(
	function ( string $class ): void {
		$prefix    = 'Enteco\\ImmoConnector\\';
		$base_dir  = __DIR__ . '/';
		$len       = strlen( $prefix );

		if ( strncmp( $prefix, $class, $len ) !== 0 ) {
			return;
		}

		$relative_class = substr( $class, $len );
		$file           = $base_dir . str_replace( '\\', '/', $relative_class ) . '.php';

		if ( file_exists( $file ) ) {
			require $file;
		}
	}
);
