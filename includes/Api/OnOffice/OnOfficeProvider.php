<?php
/**
 * OnOffice API Provider – implements ApiInterface.
 *
 * @package Enteco\ImmoConnector\Api\OnOffice
 */

declare(strict_types=1);

namespace Enteco\ImmoConnector\Api\OnOffice;

use Enteco\ImmoConnector\Api\ApiInterface;
use Enteco\ImmoConnector\Api\ApiResponse;

/**
 * Bridges OnOfficeClient + OnOfficeMapper to the common ApiInterface.
 */
final class OnOfficeProvider implements ApiInterface {

	/** OnOffice fields to request for estate list/detail. */
	private const ESTATE_FIELDS = [
		'Id', 'plz', 'ort', 'strasse', 'hausnummer', 'bundesland', 'land',
		'breitengrad', 'laengengrad', 'etage', 'kaufpreis', 'kaltmiete',
		'warmmiete', 'nebenkosten', 'heizkosten', 'kaution', 'waehrung',
		'wohnflaeche', 'nutzflaeche', 'grundstuecksflaeche', 'anzahl_zimmer',
		'objekttitel', 'objektbeschreibung', 'lage', 'ausstatt_beschr',
		'sonstige_angaben', 'objektnr_intern', 'objektnr_extern',
		'geaendert_am', 'verfuegbar_ab', 'vermietet',
	];

	public function __construct(
		private readonly OnOfficeClient $client,
		private readonly OnOfficeMapper $mapper,
	) {}

	/** {@inheritdoc} */
	public function get_property_ids(): array {
		$response = $this->client->request(
			'estate',
			[
				'data'       => [ 'Id' ],
				'filter'     => [ 'status' => [ [ 'op' => '=', 'val' => 1 ] ] ],
				'listlimit'  => 500,
				'listoffset' => 0,
			]
		);

		$elements = $response['response']['results'][0]['data']['records'] ?? [];
		$ids      = [];

		foreach ( $elements as $element ) {
			$id = (string) ( $element['id'] ?? '' );
			if ( $id ) {
				$ids[] = $id;
			}
		}

		return $ids;
	}

	/** {@inheritdoc} */
	public function get_property( string $external_id ): ApiResponse {
		$response = $this->client->request(
			'estate',
			[
				'data'   => self::ESTATE_FIELDS,
				'filter' => [ 'Id' => [ [ 'op' => '=', 'val' => (int) $external_id ] ] ],
			]
		);

		$records = $response['response']['results'][0]['data']['records'] ?? [];

		if ( empty( $records ) ) {
			throw new \RuntimeException( "OnOffice: property $external_id not found." );
		}

		$record  = (array) $records[0]['elements'];
		$images  = $this->fetch_images( $external_id );
		$record['_images'] = $images;

		return $this->mapper->map_property( $record, $external_id );
	}

	/** {@inheritdoc} */
	public function get_properties( int $limit = 20, int $offset = 0 ): array {
		$response = $this->client->request(
			'estate',
			[
				'data'       => self::ESTATE_FIELDS,
				'filter'     => [ 'status' => [ [ 'op' => '=', 'val' => 1 ] ] ],
				'listlimit'  => $limit,
				'listoffset' => $offset,
				'sortby'     => [ 'geaendert_am' => 'DESC' ],
			]
		);

		$records   = $response['response']['results'][0]['data']['records'] ?? [];
		$responses = [];

		foreach ( $records as $record ) {
			$id     = (string) ( $record['id'] ?? '' );
			$elements = (array) ( $record['elements'] ?? [] );
			$elements['_images'] = $this->fetch_images( $id );
			$responses[] = $this->mapper->map_property( $elements, $id );
		}

		return $responses;
	}

	/** {@inheritdoc} */
	public function get_agents(): array {
		$response = $this->client->request(
			'address',
			[
				'data'      => [ 'Id', 'Vorname', 'Name', 'Email', 'Telefon1' ],
				'listlimit' => 100,
			]
		);

		$records = $response['response']['results'][0]['data']['records'] ?? [];
		$agents  = [];

		foreach ( $records as $record ) {
			$id       = (string) ( $record['id'] ?? '' );
			$elements = (array) ( $record['elements'] ?? [] );
			$agents[] = $this->mapper->map_agent( $elements, $id );
		}

		return $agents;
	}

	/** {@inheritdoc} */
	public function get_slug(): string {
		return 'onoffice';
	}

	/** {@inheritdoc} */
	public function test_connection(): bool {
		try {
			$this->client->request( 'estate', [ 'data' => [ 'Id' ], 'listlimit' => 1 ] );
			return true;
		} catch ( \RuntimeException ) {
			return false;
		}
	}

	/**
	 * Fetch image URLs for an estate from OnOffice.
	 *
	 * @param string $estate_id OnOffice estate ID.
	 * @return string[]
	 */
	private function fetch_images( string $estate_id ): array {
		try {
			$response = $this->client->request(
				'file',
				[
					'estateid'        => (int) $estate_id,
					'includeImageUrl' => 'original',
				]
			);

			$records = $response['response']['results'][0]['data']['records'] ?? [];
			$urls    = [];

			foreach ( $records as $record ) {
				$url = (string) ( $record['elements']['url'] ?? '' );
				if ( $url ) {
					$urls[] = $url;
				}
			}

			return $urls;
		} catch ( \RuntimeException ) {
			return [];
		}
	}
}
