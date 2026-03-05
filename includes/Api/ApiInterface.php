<?php
/**
 * Contract for all API provider implementations.
 *
 * @package Enteco\ImmoConnector\Api
 */

declare(strict_types=1);

namespace Enteco\ImmoConnector\Api;

/**
 * All providers (Justimmo, OnOffice, …) must implement this interface.
 */
interface ApiInterface {

	/**
	 * Return all active property IDs from the provider.
	 *
	 * @return string[] Array of external IDs.
	 * @throws \RuntimeException On connection or auth failure.
	 */
	public function get_property_ids(): array;

	/**
	 * Return normalized property data for a given external ID.
	 *
	 * @param string $external_id The provider's ID for this property.
	 * @return ApiResponse
	 * @throws \RuntimeException On connection or auth failure.
	 */
	public function get_property( string $external_id ): ApiResponse;

	/**
	 * Return a list of normalized property data (paginated batch).
	 *
	 * @param int $limit  Number of records to fetch.
	 * @param int $offset Zero-based offset.
	 * @return ApiResponse[]
	 * @throws \RuntimeException On connection or auth failure.
	 */
	public function get_properties( int $limit = 20, int $offset = 0 ): array;

	/**
	 * Return all agent/employee records from the provider.
	 *
	 * @return ApiResponse[]
	 * @throws \RuntimeException On connection or auth failure.
	 */
	public function get_agents(): array;

	/** Return the provider slug (justimmo|onoffice). */
	public function get_slug(): string;

	/** Test whether credentials are valid (should not throw, returns bool). */
	public function test_connection(): bool;
}
