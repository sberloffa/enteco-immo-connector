<?php
/**
 * Orchestrates a full manual import run with locking and object-limit enforcement.
 */

declare( strict_types=1 );

namespace Enteco\ImmoConnector\Import;

use Enteco\ImmoConnector\Api\ApiInterface;
use Enteco\ImmoConnector\Api\Justimmo\JustimmoClient;
use Enteco\ImmoConnector\Api\OnOffice\OnOfficeClient;
use Enteco\ImmoConnector\OpenImmo\Mapper;
use Enteco\ImmoConnector\PostTypes\PropertyPostType;

class ImportEngine {

	private const LOCK_TRANSIENT = 'eic_import_lock';
	private const LOCK_TTL       = 180;  // seconds
	private const LOCK_OVERRIDE  = 600;  // 10 minutes failsafe

	/** Runs the full import cycle and returns a completed ImportJob. */
	public function run(): ImportJob {
		$job = new ImportJob();

		if ( ! $this->acquire_lock( $job ) ) {
			return $job;
		}

		try {
			$client = $this->resolve_client();
			if ( $client === null ) {
				$job->fail(
					__( 'Kein Provider konfiguriert oder Zugangsdaten fehlen.', 'enteco-immo-connector' )
				);
				return $job;
			}

			if ( ! $this->check_object_limit( $job ) ) {
				return $job;
			}

			$provider = (string) get_option( 'eic_provider', '' );

			// Import agents first so property relations can be resolved later.
			$agents_resp = $client->get_agents();
			if ( $agents_resp->is_success() ) {
				$agent_importer = new AgentImporter();
				foreach ( $agents_resp->get_data() as $raw ) {
					$normalized = $provider === 'justimmo'
						? Mapper::agent_from_justimmo( $raw )
						: Mapper::agent_from_onoffice( $raw );
					$agent_importer->upsert( $normalized );
				}
			}

			// Import properties.
			$props_resp = $client->get_properties();
			if ( ! $props_resp->is_success() ) {
				$job->fail( $props_resp->get_error_message() );
				return $job;
			}

			$property_importer = new PropertyImporter();
			foreach ( $props_resp->get_data() as $raw ) {
				$normalized = $provider === 'justimmo'
					? Mapper::from_justimmo( $raw )
					: Mapper::from_onoffice( $raw );

				if ( $property_importer->upsert( $normalized ) ) {
					$job->increment_success();
				} else {
					$job->increment_error();
				}
			}

			$job->complete();

		} catch ( \Throwable $e ) {
			$job->fail( $e->getMessage() );
		} finally {
			$this->release_lock();
		}

		return $job;
	}

	private function acquire_lock( ImportJob $job ): bool {
		$existing = get_transient( self::LOCK_TRANSIENT );

		if ( $existing !== false ) {
			$lock_time = (int) get_option( 'eic_import_lock_time', 0 );
			if ( ( time() - $lock_time ) < self::LOCK_OVERRIDE ) {
				$job->fail(
					__( 'Import läuft bereits. Bitte warte und versuche es erneut.', 'enteco-immo-connector' )
				);
				return false;
			}
		}

		set_transient( self::LOCK_TRANSIENT, '1', self::LOCK_TTL );
		update_option( 'eic_import_lock_time', time(), false );

		return true;
	}

	private function release_lock(): void {
		delete_transient( self::LOCK_TRANSIENT );
		delete_option( 'eic_import_lock_time' );
	}

	private function resolve_client(): ?ApiInterface {
		$provider = (string) get_option( 'eic_provider', '' );

		return match ( $provider ) {
			'justimmo' => new JustimmoClient(
				(string) get_option( 'eic_justimmo_username', '' ),
				(string) get_option( 'eic_justimmo_password', '' )
			),
			'onoffice' => new OnOfficeClient(
				(string) get_option( 'eic_onoffice_token', '' ),
				(string) get_option( 'eic_onoffice_secret', '' )
			),
			default    => null,
		};
	}

	private function check_object_limit( ImportJob $job ): bool {
		$limit = (int) apply_filters( 'eic_object_limit', 50 );

		$counts = wp_count_posts( PropertyPostType::POST_TYPE );
		$count  = (int) ( $counts->publish ?? 0 ) + (int) ( $counts->draft ?? 0 );

		if ( $count >= $limit ) {
			$job->fail(
				sprintf(
					/* translators: 1: current count, 2: limit */
					__( 'Objektlimit erreicht (%1$d/%2$d). Upgrade auf PRO für unlimitierte Objekte.', 'enteco-immo-connector' ),
					$count,
					$limit
				)
			);
			return false;
		}

		return true;
	}
}
