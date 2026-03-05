<?php
/**
 * HTTP client for OnOffice API (HMAC v2).
 *
 * @package Enteco\ImmoConnector\Api\OnOffice
 */

declare(strict_types=1);

namespace Enteco\ImmoConnector\Api\OnOffice;

/**
 * Low-level wrapper for OnOffice API requests.
 */
final class OnOfficeClient {

	private const BASE_URL      = 'https://api.onoffice.de/api/stable/api.php';
	private const TIMEOUT       = 30;
	private const ACTION_ID     = 'urn:onoffice-de-ns:smart:2.5:smartml:action:read';

	/**
	 * @param string $token  OnOffice API token.
	 * @param string $secret OnOffice API secret.
	 */
	public function __construct(
		private readonly string $token,
		private readonly string $secret,
	) {}

	/**
	 * Send an action request to the OnOffice API.
	 *
	 * @param string               $resource_type 'estate', 'address', 'file', etc.
	 * @param array<string, mixed> $parameters    Action parameters.
	 * @return array<string, mixed> Decoded JSON response.
	 * @throws \RuntimeException On HTTP or API errors.
	 */
	public function request( string $resource_type, array $parameters = [] ): array {
		$timestamp = time();
		$hmac      = $this->build_hmac( self::ACTION_ID, $resource_type, $timestamp );

		$body = json_encode(
			[
				'token'   => $this->token,
				'request' => [
					'actions' => [
						[
							'actionid'     => self::ACTION_ID,
							'resourceid'   => '',
							'identifier'   => '',
							'resourcetype' => $resource_type,
							'timestamp'    => $timestamp,
							'hmac'         => $hmac,
							'hmac_version' => 2,
							'parameters'   => $parameters,
						],
					],
				],
			],
			JSON_THROW_ON_ERROR
		);

		$response = wp_remote_post(
			self::BASE_URL,
			[
				'headers' => [ 'Content-Type' => 'application/json' ],
				'body'    => $body,
				'timeout' => self::TIMEOUT,
			]
		);

		if ( is_wp_error( $response ) ) {
			throw new \RuntimeException(
				'OnOffice API error: ' . $response->get_error_message()
			);
		}

		$status_code = wp_remote_retrieve_response_code( $response );

		if ( $status_code < 200 || $status_code >= 300 ) {
			throw new \RuntimeException(
				sprintf( 'OnOffice API returned HTTP %d', $status_code )
			);
		}

		$decoded = json_decode( wp_remote_retrieve_body( $response ), true, 512, JSON_THROW_ON_ERROR );

		// Check for API-level error.
		$status = $decoded['response']['results'][0]['status']['code'] ?? 0;
		if ( (int) $status < 200 || (int) $status >= 300 ) {
			$msg = $decoded['response']['results'][0]['status']['message'] ?? 'Unknown OnOffice error';
			throw new \RuntimeException( 'OnOffice API error: ' . $msg );
		}

		return $decoded;
	}

	/**
	 * Build HMAC v2 signature.
	 *
	 * @param string $action_id     Action identifier.
	 * @param string $resource_type Resource type.
	 * @param int    $timestamp     Unix timestamp.
	 */
	private function build_hmac( string $action_id, string $resource_type, int $timestamp ): string {
		$data = $action_id . $resource_type . (string) $timestamp . $this->token;
		return hash_hmac( 'sha256', $data, $this->secret );
	}
}
