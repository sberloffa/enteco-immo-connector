<?php
/**
 * Import Job – represents a single import run.
 *
 * @package Enteco\ImmoConnector\Import
 */

declare(strict_types=1);

namespace Enteco\ImmoConnector\Import;

use Enteco\ImmoConnector\Api\ApiInterface;

/**
 * Orchestrates one complete import cycle for a single provider.
 */
final class ImportJob {

	private int    $new_count     = 0;
	private int    $updated_count = 0;
	private int    $removed_count = 0;
	private int    $error_count   = 0;
	private array  $errors        = [];
	private string $status        = 'idle';

	public function __construct(
		private readonly ApiInterface    $provider,
		private readonly PropertyImporter $property_importer,
		private readonly AgentImporter    $agent_importer,
	) {}

	/**
	 * Execute the import job (manual, no Cron in FREE tier).
	 *
	 * @param int $batch_size Max properties to process in one run.
	 * @param int $offset     Starting offset.
	 */
	public function run( int $batch_size = 20, int $offset = 0 ): void {
		$this->status = 'running';

		try {
			do_action( 'eic/import/before_import', $this->provider->get_slug() );

			// Fetch and import properties.
			$properties = $this->provider->get_properties( $batch_size, $offset );

			foreach ( $properties as $response ) {
				// Add 200ms delay to respect rate limits.
				usleep( 200000 );

				try {
					$existing_hash = null;
					$diff          = new ImportDiff( $this->provider->get_slug(), [ $response->get_external_id() ] );
					$post_id       = $diff->find_post_id( $response->get_external_id() );

					if ( null !== $post_id && ! $diff->needs_update( $post_id, $response->build_hash() ) ) {
						continue; // No changes.
					}

					$is_new = null === $post_id;
					$this->property_importer->import( $response );

					if ( $is_new ) {
						++$this->new_count;
					} else {
						++$this->updated_count;
					}
				} catch ( \Throwable $e ) {
					++$this->error_count;
					$this->errors[] = $e->getMessage();
				}
			}

			// Import agents.
			try {
				$agents = $this->provider->get_agents();
				foreach ( $agents as $agent ) {
					$this->agent_importer->import( $agent );
				}
			} catch ( \Throwable $e ) {
				$this->errors[] = 'Agent import: ' . $e->getMessage();
			}

			$this->status = 'done';

			do_action( 'eic/import/after_import', $this->provider->get_slug(), $this->get_summary() );

		} catch ( \Throwable $e ) {
			$this->status = 'failed';
			++$this->error_count;
			$this->errors[] = $e->getMessage();
		}

		// Store last-run status.
		update_option(
			'eic_last_import_status',
			[
				'time'    => current_time( 'mysql' ),
				'status'  => $this->status,
				'summary' => $this->get_summary(),
			],
			false
		);
	}

	/** @return array<string, mixed> */
	public function get_summary(): array {
		return [
			'source'    => $this->provider->get_slug(),
			'status'    => $this->status,
			'new'       => $this->new_count,
			'updated'   => $this->updated_count,
			'removed'   => $this->removed_count,
			'errors'    => $this->error_count,
			'error_log' => $this->errors,
		];
	}

	public function get_status(): string {
		return $this->status;
	}
}
