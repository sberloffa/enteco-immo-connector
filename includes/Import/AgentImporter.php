<?php
/**
 * Agent Importer – creates/updates eic_agent posts.
 *
 * @package Enteco\ImmoConnector\Import
 */

declare(strict_types=1);

namespace Enteco\ImmoConnector\Import;

use Enteco\ImmoConnector\Api\ApiResponse;

/**
 * Persists agent data as eic_agent CPT posts.
 */
final class AgentImporter {

	/**
	 * Import or update a single agent.
	 *
	 * @param ApiResponse $response Normalized agent data.
	 * @return int WP post ID.
	 */
	public function import( ApiResponse $response ): int {
		$post_id = $this->find_post_id(
			$response->get_source(),
			$response->get_external_id()
		);

		if ( null === $post_id ) {
			$post_id = $this->create_post( $response );
		}

		$this->save_meta( $post_id, $response );

		return $post_id;
	}

	/**
	 * Find an existing eic_agent post by source + external ID.
	 *
	 * @param string $source      Provider slug.
	 * @param string $external_id Provider's agent ID.
	 * @return int|null
	 */
	private function find_post_id( string $source, string $external_id ): ?int {
		$query = new \WP_Query(
			[
				'post_type'   => 'eic_agent',
				'post_status' => [ 'publish', 'draft' ],
				'meta_query'  => [
					[
						'key'   => 'eic_api_source',
						'value' => $source,
					],
					[
						'key'   => 'eic_api_source_id',
						'value' => $external_id,
					],
				],
				'fields'         => 'ids',
				'posts_per_page' => 1,
				'no_found_rows'  => true,
			]
		);

		$ids = $query->posts;
		return ! empty( $ids ) ? (int) $ids[0] : null;
	}

	/** Create a new eic_agent post. */
	private function create_post( ApiResponse $response ): int {
		$vorname  = (string) ( $response->get_field( 'eic_vorname' ) ?? '' );
		$nachname = (string) ( $response->get_field( 'eic_nachname' ) ?? '' );
		$title    = trim( "$vorname $nachname" ) ?: 'Makler ' . $response->get_external_id();

		$post_id = wp_insert_post(
			[
				'post_type'   => 'eic_agent',
				'post_status' => 'publish',
				'post_title'  => $title,
			],
			true
		);

		if ( is_wp_error( $post_id ) ) {
			throw new \RuntimeException( 'Failed to create agent: ' . $post_id->get_error_message() );
		}

		return $post_id;
	}

	/** Save agent meta fields. */
	private function save_meta( int $post_id, ApiResponse $response ): void {
		foreach ( $response->get_fields() as $key => $value ) {
			if ( str_starts_with( $key, 'eic_' ) ) {
				update_post_meta( $post_id, $key, $value );
			}
		}
	}
}
