<?php
/**
 * onOffice enterprise API v2 client.
 * Authentication: HMAC-SHA256 per-request signature.
 * Docs: https://apidoc.onoffice.de
 */

declare( strict_types=1 );

namespace Enteco\ImmoConnector\Api\OnOffice;

use Enteco\ImmoConnector\Api\ApiInterface;
use Enteco\ImmoConnector\Api\ApiResponse;

class OnOfficeClient implements ApiInterface {

	private const API_URL = 'https://api.onoffice.de/api/stable/api.php';

	private string $token;
	private string $secret;

	public function __construct( string $token, string $secret ) {
		$this->token  = $token;
		$this->secret = $secret;
	}

	public function get_properties(): ApiResponse {
		return $this->read_estates(
			[
				'Id', 'objektnr_extern', 'objekttitel',
				'freitext_objektbeschreibung', 'vermarktungsart', 'objektart',
				'nutzungsart', 'zustand',
				'kaufpreis', 'kaltmiete', 'warmmiete', 'waehrung',
				'wohnflaeche', 'nutzflaeche', 'grundstuecksflaeche', 'anzahl_zimmer',
				'strasse', 'hausnummer', 'plz', 'ort', 'land', 'breitengrad', 'laengengrad',
				'balkon', 'garage', 'garten', 'fahrstuhl', 'barrierefrei',
				'keller', 'einbaukueche', 'stellplatz_art', 'terrasse', 'haustiere',
				'titelbild',
			]
		);
	}

	public function get_property( string $id ): ApiResponse {
		return $this->read_estates(
			[ 'Id', 'objekttitel', 'kaufpreis', 'kaltmiete' ],
			[ 'Id' => [ 'op' => '=', 'val' => [ $id ] ] ],
			1
		);
	}

	public function get_agents(): ApiResponse {
		return $this->read_addresses( [ 'KdNr', 'Vorname', 'Name', 'Email', 'Telefon1' ] );
	}

	public function test_connection(): ApiResponse {
		return $this->read_estates( [ 'Id' ], [], 1 );
	}

	/**
	 * @param list<string>         $columns
	 * @param array<string, mixed> $filter
	 */
	private function read_estates( array $columns, array $filter = [], int $limit = 50 ): ApiResponse {
		return $this->send_request(
			'urn:onoffice-de-ns:smart:2.5:smartml:action:read',
			'estate',
			[
				'data'         => $columns,
				'listlimit'    => $limit,
				'recordids'    => [],
				'filter'       => $filter,
				'sortby'       => 'Id',
				'sortorder'    => 'ASC',
				'formatoutput' => true,
			]
		);
	}

	/** @param list<string> $columns */
	private function read_addresses( array $columns ): ApiResponse {
		return $this->send_request(
			'urn:onoffice-de-ns:smart:2.5:smartml:action:read',
			'address',
			[
				'data'      => $columns,
				'listlimit' => 100,
			]
		);
	}

	/** @param array<string, mixed> $parameters */
	private function send_request( string $action_id, string $resource, array $parameters ): ApiResponse {
		if ( empty( $this->token ) || empty( $this->secret ) ) {
			return ApiResponse::error(
				__( 'OnOffice-Zugangsdaten fehlen.', 'enteco-immo-connector' )
			);
		}

		$timestamp = time();
		$hmac      = $this->build_hmac( $action_id, $timestamp, $resource );

		$body = (string) wp_json_encode( [
			'token'   => $this->token,
			'request' => [
				'actions' => [
					[
						'actionid'     => $action_id,
						'resourceid'   => '',
						'resourcetype' => $resource,
						'identifier'   => '',
						'timestamp'    => $timestamp,
						'hmac'         => $hmac,
						'hmac_version' => '2',
						'parameters'   => $parameters,
					],
				],
			],
		] );

		$response = wp_remote_post(
			self::API_URL,
			[
				'headers' => [
					'Content-Type' => 'application/json',
					'Accept'       => 'application/json',
				],
				'body'    => $body,
				'timeout' => 30,
			]
		);

		if ( is_wp_error( $response ) ) {
			return ApiResponse::error( $response->get_error_message() );
		}

		$status  = (int) wp_remote_retrieve_response_code( $response );
		$raw     = wp_remote_retrieve_body( $response );
		$decoded = json_decode( $raw, true );

		if ( $status !== 200 || ! is_array( $decoded ) ) {
			return ApiResponse::error(
				/* translators: %d: HTTP status code */
				sprintf( __( 'OnOffice API Fehler (HTTP %d).', 'enteco-immo-connector' ), $status ),
				$status
			);
		}

		$records = $decoded['response']['results'][0]['data']['records'] ?? null;
		if ( ! is_array( $records ) ) {
			// Check for API-level error message.
			$msg = $decoded['response']['results'][0]['status']['message'] ?? '';
			if ( $msg ) {
				return ApiResponse::error( (string) $msg );
			}
			return ApiResponse::success( [] );
		}

		return ApiResponse::success( array_values( $records ) );
	}

	private function build_hmac( string $action_id, int $timestamp, string $resource_type ): string {
		// onOffice HMAC v2: token + timestamp + actionid + resourcetype
		$hash_input = implode( '', [ $this->token, $timestamp, $action_id, $resource_type ] );
		return hash_hmac( 'sha256', $hash_input, $this->secret );
	}
}
