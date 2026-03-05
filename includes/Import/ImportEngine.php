<?php
/**
 * Import Engine – top-level orchestrator for manual imports (FREE).
 *
 * @package Enteco\ImmoConnector\Import
 */

declare(strict_types=1);

namespace Enteco\ImmoConnector\Import;

use Enteco\ImmoConnector\Api\Justimmo\JustimmoClient;
use Enteco\ImmoConnector\Api\Justimmo\JustimmoMapper;
use Enteco\ImmoConnector\Api\Justimmo\JustimmoProvider;
use Enteco\ImmoConnector\Api\OnOffice\OnOfficeClient;
use Enteco\ImmoConnector\Api\OnOffice\OnOfficeMapper;
use Enteco\ImmoConnector\Api\OnOffice\OnOfficeProvider;
use Enteco\ImmoConnector\Api\ApiInterface;

/**
 * Builds the configured provider and runs a manual import job.
 */
final class ImportEngine {

	private const LOCK_TRANSIENT = 'eic_import_lock';
	private const LOCK_TTL       = 180; // 3 minutes.

	/**
	 * Run a manual import for the configured provider.
	 *
	 * @return array<string, mixed> Import summary.
	 * @throws \RuntimeException On lock conflict or misconfiguration.
	 */
	public function run_manual(): array {
		if ( get_transient( self::LOCK_TRANSIENT ) ) {
			throw new \RuntimeException(
				__( 'Ein Import läuft bereits. Bitte warten Sie.', 'enteco-immo-connector' )
			);
		}

		set_transient( self::LOCK_TRANSIENT, time(), self::LOCK_TTL );

		try {
			$provider = $this->build_provider();
			$job      = $this->build_job( $provider );
			$job->run();
			return $job->get_summary();
		} finally {
			delete_transient( self::LOCK_TRANSIENT );
		}
	}

	/**
	 * Test the configured provider connection.
	 *
	 * @return bool
	 */
	public function test_connection(): bool {
		try {
			$provider = $this->build_provider();
			return $provider->test_connection();
		} catch ( \Throwable ) {
			return false;
		}
	}

	/**
	 * Build the active provider instance from saved options.
	 *
	 * @return ApiInterface
	 * @throws \RuntimeException If no provider is configured.
	 */
	private function build_provider(): ApiInterface {
		$active = (string) get_option( 'eic_active_provider', '' );

		return match ( $active ) {
			'justimmo' => $this->build_justimmo(),
			'onoffice'  => $this->build_onoffice(),
			default    => throw new \RuntimeException(
				__( 'Kein API-Provider konfiguriert. Bitte gehen Sie zu Einstellungen.', 'enteco-immo-connector' )
			),
		};
	}

	/** Build Justimmo provider from saved credentials. */
	private function build_justimmo(): JustimmoProvider {
		$username = (string) get_option( 'eic_justimmo_username', '' );
		$password = (string) get_option( 'eic_justimmo_password', '' );

		if ( ! $username || ! $password ) {
			throw new \RuntimeException(
				__( 'Justimmo-Zugangsdaten fehlen. Bitte tragen Sie Benutzername und Passwort ein.', 'enteco-immo-connector' )
			);
		}

		return new JustimmoProvider(
			new JustimmoClient( $username, $password ),
			new JustimmoMapper()
		);
	}

	/** Build OnOffice provider from saved credentials. */
	private function build_onoffice(): OnOfficeProvider {
		$token  = (string) get_option( 'eic_onoffice_token', '' );
		$secret = (string) get_option( 'eic_onoffice_secret', '' );

		if ( ! $token || ! $secret ) {
			throw new \RuntimeException(
				__( 'OnOffice-Zugangsdaten fehlen. Bitte tragen Sie Token und Secret ein.', 'enteco-immo-connector' )
			);
		}

		return new OnOfficeProvider(
			new OnOfficeClient( $token, $secret ),
			new OnOfficeMapper()
		);
	}

	/** Build an ImportJob for the given provider. */
	private function build_job( ApiInterface $provider ): ImportJob {
		$diff     = new ImportDiff( $provider->get_slug(), [] );
		$media    = new MediaHandler();
		$importer = new PropertyImporter( $media, $diff );
		$agent    = new AgentImporter();

		return new ImportJob( $provider, $importer, $agent );
	}
}
