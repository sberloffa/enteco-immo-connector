<?php
/**
 * Contract every provider client must fulfill.
 */

declare( strict_types=1 );

namespace Enteco\ImmoConnector\Api;

interface ApiInterface {

	/**
	 * Returns a paginated list of raw property records.
	 */
	public function get_properties(): ApiResponse;

	/**
	 * Returns a single property by provider-specific ID.
	 */
	public function get_property( string $id ): ApiResponse;

	/**
	 * Returns a list of agent/broker records.
	 */
	public function get_agents(): ApiResponse;

	/**
	 * Validates credentials with a lightweight API ping.
	 */
	public function test_connection(): ApiResponse;
}
