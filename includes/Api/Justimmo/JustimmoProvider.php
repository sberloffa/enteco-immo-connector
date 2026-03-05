<?php
/**
 * Justimmo API Provider – implements ApiInterface.
 *
 * @package Enteco\ImmoConnector\Api\Justimmo
 */

declare(strict_types=1);

namespace Enteco\ImmoConnector\Api\Justimmo;

use Enteco\ImmoConnector\Api\ApiInterface;
use Enteco\ImmoConnector\Api\ApiResponse;

/**
 * Bridges JustimmoClient + JustimmoMapper to the common ApiInterface.
 */
final class JustimmoProvider implements ApiInterface {

	public function __construct(
		private readonly JustimmoClient $client,
		private readonly JustimmoMapper $mapper,
	) {}

	/** {@inheritdoc} */
	public function get_property_ids(): array {
		$xml_string = $this->client->get( 'objekt/ids' );
		$xml        = $this->client->parse_xml( $xml_string );
		$ids        = [];

		foreach ( $xml->xpath( './/objekt_id' ) ?: [] as $node ) {
			$id = (string) $node;
			if ( $id ) {
				$ids[] = $id;
			}
		}

		return $ids;
	}

	/** {@inheritdoc} */
	public function get_property( string $external_id ): ApiResponse {
		$xml_string = $this->client->get( 'objekt/detail', [ 'objekt_id' => $external_id ] );
		$xml        = $this->client->parse_xml( $xml_string );

		return $this->mapper->map_property( $xml, $external_id );
	}

	/** {@inheritdoc} */
	public function get_properties( int $limit = 20, int $offset = 0 ): array {
		$xml_string = $this->client->get(
			'objekt/list',
			[ 'limit' => $limit, 'offset' => $offset ]
		);
		$xml        = $this->client->parse_xml( $xml_string );
		$responses  = [];

		foreach ( $xml->xpath( './/immobilie' ) ?: [] as $node ) {
			$id = (string) ( $node->verwaltung_techn->objektnr_extern ?? '' );
			if ( ! $id ) {
				// Fallback to internal ID.
				$id = (string) ( $node->verwaltung_techn->objektnr_intern ?? uniqid( 'jm_', true ) );
			}
			$responses[] = $this->mapper->map_property( $node, $id );
		}

		return $responses;
	}

	/** {@inheritdoc} */
	public function get_agents(): array {
		$xml_string = $this->client->get( 'team/list' );
		$xml        = $this->client->parse_xml( $xml_string );
		$agents     = [];

		foreach ( $xml->xpath( './/mitarbeiter' ) ?: [] as $node ) {
			$id       = (string) ( $node->id ?? uniqid( 'jm_agent_', true ) );
			$agents[] = $this->mapper->map_agent( $node, $id );
		}

		return $agents;
	}

	/** {@inheritdoc} */
	public function get_slug(): string {
		return 'justimmo';
	}

	/** {@inheritdoc} */
	public function test_connection(): bool {
		try {
			$this->client->get( 'objekt/list', [ 'limit' => 1, 'offset' => 0 ] );
			return true;
		} catch ( \RuntimeException ) {
			return false;
		}
	}
}
