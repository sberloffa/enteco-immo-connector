<?php
/**
 * Import Diff – compares incoming IDs against existing WP posts.
 *
 * @package Enteco\ImmoConnector\Import
 */

declare(strict_types=1);

namespace Enteco\ImmoConnector\Import;

/**
 * Determines which properties need to be created, updated, or trashed.
 */
final class ImportDiff {

	/**
	 * @param string   $source     Provider slug (justimmo|onoffice).
	 * @param string[] $remote_ids IDs currently active at the provider.
	 */
	public function __construct(
		private readonly string $source,
		private readonly array  $remote_ids,
	) {}

	/**
	 * Return IDs that are new (not yet in WP).
	 *
	 * @return string[]
	 */
	public function get_new_ids(): array {
		$existing = $this->get_existing_ids();
		return array_values( array_diff( $this->remote_ids, $existing ) );
	}

	/**
	 * Return IDs that exist in WP but are no longer at the provider.
	 *
	 * @return string[]
	 */
	public function get_removed_ids(): array {
		$existing = $this->get_existing_ids();
		return array_values( array_diff( $existing, $this->remote_ids ) );
	}

	/**
	 * Return all IDs that exist both remotely and locally (potential updates).
	 *
	 * @return string[]
	 */
	public function get_existing_remote_ids(): array {
		$existing = $this->get_existing_ids();
		return array_values( array_intersect( $this->remote_ids, $existing ) );
	}

	/**
	 * Find the WP post ID for a given external ID.
	 *
	 * @param string $external_id Provider's external ID.
	 * @return int|null WP post ID, or null if not found.
	 */
	public function find_post_id( string $external_id ): ?int {
		$query = new \WP_Query(
			[
				'post_type'   => 'eic_property',
				'post_status' => [ 'publish', 'draft', 'private' ],
				'meta_query'  => [
					[
						'key'   => 'eic_api_source',
						'value' => $this->source,
					],
					[
						'key'   => 'eic_api_source_id',
						'value' => $external_id,
					],
				],
				'fields'           => 'ids',
				'posts_per_page'   => 1,
				'no_found_rows'    => true,
				'suppress_filters' => true,
			]
		);

		$ids = $query->posts;
		return ! empty( $ids ) ? (int) $ids[0] : null;
	}

	/**
	 * Check whether a property needs updating based on hash.
	 *
	 * @param int    $post_id       WP post ID.
	 * @param string $incoming_hash Hash of the incoming normalized data.
	 */
	public function needs_update( int $post_id, string $incoming_hash ): bool {
		$stored_hash = get_post_meta( $post_id, 'eic_import_hash', true );
		return $stored_hash !== $incoming_hash;
	}

	/**
	 * Load all existing eic_api_source_id values for this source.
	 *
	 * @return string[]
	 */
	private function get_existing_ids(): array {
		global $wpdb;

		// phpcs:disable WordPress.DB.DirectDatabaseQuery
		$results = $wpdb->get_col(
			$wpdb->prepare(
				"SELECT pm_id.meta_value
				FROM {$wpdb->postmeta} pm_src
				JOIN {$wpdb->postmeta} pm_id
				    ON pm_src.post_id = pm_id.post_id
				WHERE pm_src.meta_key = 'eic_api_source'
				    AND pm_src.meta_value = %s
				    AND pm_id.meta_key = 'eic_api_source_id'",
				$this->source
			)
		);
		// phpcs:enable

		return array_map( 'strval', (array) $results );
	}
}
