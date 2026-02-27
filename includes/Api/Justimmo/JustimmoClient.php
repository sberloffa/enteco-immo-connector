<?php
/**
 * Justimmo REST API v2 client.
 * Docs: https://api.justimmo.at/rest/v2
 */

declare( strict_types=1 );

namespace Enteco\ImmoConnector\Api\Justimmo;

use Enteco\ImmoConnector\Api\ApiInterface;
use Enteco\ImmoConnector\Api\ApiResponse;

class JustimmoClient implements ApiInterface {

	private const API_BASE = 'https://api.justimmo.at/rest/v2/';

	private string $username;
	private string $password;

	public function __construct( string $username, string $password ) {
		$this->username = $username;
		$this->password = $password;
	}

	public function get_properties(): ApiResponse {
		return $this->request( 'realty', [ 'limit' => 50 ] );
	}

	public function get_property( string $id ): ApiResponse {
		return $this->request( 'realty/' . rawurlencode( $id ) );
	}

	public function get_agents(): ApiResponse {
		return $this->request( 'employee' );
	}

	public function test_connection(): ApiResponse {
		return $this->request( 'realty', [ 'limit' => 1 ] );
	}

	/** @param array<string, mixed> $params */
	private function request( string $endpoint, array $params = [] ): ApiResponse {
		if ( empty( $this->username ) || empty( $this->password ) ) {
			return ApiResponse::error(
				__( 'Justimmo-Zugangsdaten fehlen.', 'enteco-immo-connector' )
			);
		}

		$url = self::API_BASE . $endpoint;
		if ( ! empty( $params ) ) {
			$url = add_query_arg( $params, $url );
		}

		$response = wp_remote_get(
			$url,
			[
				'headers' => [
					'Authorization' => 'Basic ' . base64_encode( $this->username . ':' . $this->password ),
					'Accept'        => 'application/json',
				],
				'timeout' => 30,
			]
		);

		if ( is_wp_error( $response ) ) {
			return ApiResponse::error( $response->get_error_message() );
		}

		$status = (int) wp_remote_retrieve_response_code( $response );
		$body   = wp_remote_retrieve_body( $response );

		if ( $status !== 200 ) {
			return ApiResponse::error(
				/* translators: %d: HTTP status code */
				sprintf( __( 'Justimmo API Fehler (HTTP %d).', 'enteco-immo-connector' ), $status ),
				$status
			);
		}

		$decoded = json_decode( $body, true );
		if ( ! is_array( $decoded ) ) {
			return ApiResponse::error( __( 'Justimmo: Ungültige API-Antwort.', 'enteco-immo-connector' ) );
		}

		$items = $decoded['data'] ?? $decoded;
		return ApiResponse::success( array_values( is_array( $items ) ? $items : [] ) );
	}
}
