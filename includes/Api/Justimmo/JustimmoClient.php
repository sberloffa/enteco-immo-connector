<?php
/**
 * HTTP client for Justimmo REST API v1.
 *
 * @package Enteco\ImmoConnector\Api\Justimmo
 */

declare(strict_types=1);

namespace Enteco\ImmoConnector\Api\Justimmo;

/**
 * Low-level HTTP wrapper for Justimmo API.
 * Uses wp_remote_get with Basic Auth.
 */
final class JustimmoClient {

	private const BASE_URL = 'https://api.justimmo.at/rest/v1/';
	private const TIMEOUT  = 30;

	/**
	 * @param string $username Justimmo API username.
	 * @param string $password Justimmo API password.
	 */
	public function __construct(
		private readonly string $username,
		private readonly string $password,
	) {}

	/**
	 * Make a GET request to the Justimmo API.
	 *
	 * @param string               $endpoint Path after base URL (e.g. 'objekt/list').
	 * @param array<string, mixed> $params   Query parameters.
	 * @return string Raw response body (XML).
	 * @throws \RuntimeException On HTTP errors.
	 */
	public function get( string $endpoint, array $params = [] ): string {
		$url = self::BASE_URL . ltrim( $endpoint, '/' );

		if ( ! empty( $params ) ) {
			$url .= '?' . http_build_query( $params );
		}

		$transient_key = 'eic_jm_' . md5( $url );
		$cached        = get_transient( $transient_key );

		if ( false !== $cached ) {
			return (string) $cached;
		}

		$response = wp_remote_get(
			$url,
			[
				'headers' => [
					'Authorization' => 'Basic ' . base64_encode( $this->username . ':' . $this->password ),
					'Accept'        => 'application/xml',
				],
				'timeout' => self::TIMEOUT,
			]
		);

		if ( is_wp_error( $response ) ) {
			throw new \RuntimeException(
				sprintf(
					'Justimmo API error: %s',
					$response->get_error_message()
				)
			);
		}

		$status_code = wp_remote_retrieve_response_code( $response );

		if ( $status_code < 200 || $status_code >= 300 ) {
			throw new \RuntimeException(
				sprintf( 'Justimmo API returned HTTP %d for %s', $status_code, esc_url_raw( $url ) )
			);
		}

		$body = wp_remote_retrieve_body( $response );

		// Cache detail responses for 1 hour.
		if ( str_contains( $endpoint, 'detail' ) ) {
			set_transient( $transient_key, $body, HOUR_IN_SECONDS );
		}

		return $body;
	}

	/**
	 * Safely parse XML with XXE protection.
	 *
	 * @param string $xml Raw XML string.
	 * @return \SimpleXMLElement
	 * @throws \RuntimeException On parse failure.
	 */
	public function parse_xml( string $xml ): \SimpleXMLElement {
		$previous = libxml_use_internal_errors( true );
		$element  = simplexml_load_string( $xml, \SimpleXMLElement::class, LIBXML_NONET );
		libxml_use_internal_errors( $previous );

		if ( false === $element ) {
			$errors = libxml_get_errors();
			libxml_clear_errors();
			$msg = ! empty( $errors ) ? $errors[0]->message : 'Unknown XML parse error';
			throw new \RuntimeException( 'Justimmo XML parse error: ' . $msg );
		}

		return $element;
	}
}
