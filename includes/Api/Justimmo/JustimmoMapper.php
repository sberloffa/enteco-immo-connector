<?php
/**
 * Maps Justimmo XML response → normalized ApiResponse.
 *
 * @package Enteco\ImmoConnector\Api\Justimmo
 */

declare(strict_types=1);

namespace Enteco\ImmoConnector\Api\Justimmo;

use Enteco\ImmoConnector\Api\ApiResponse;
use Enteco\ImmoConnector\OpenImmo\Mapper;
use Enteco\ImmoConnector\OpenImmo\Schema;
use Enteco\ImmoConnector\PostTypes\FieldEngine\FieldDefinitions;

/**
 * Transforms Justimmo XML (OpenImmo-like) into normalized ApiResponse objects.
 */
final class JustimmoMapper extends Mapper {

	/**
	 * Map a Justimmo XML detail response to an ApiResponse.
	 *
	 * @param \SimpleXMLElement $xml     The parsed XML detail node.
	 * @param string            $ext_id  The Justimmo property ID.
	 * @return ApiResponse
	 */
	public function map_property( \SimpleXMLElement $xml, string $ext_id ): ApiResponse {
		$field_map     = Schema::get_field_map();
		$field_types   = $this->build_type_map();
		$fields        = [];

		// Flatten all scalar XML elements into a key=>value array.
		$raw = $this->flatten_xml( $xml );

		foreach ( $field_map as $openimmo_key => $eic_key ) {
			if ( ! isset( $raw[ $openimmo_key ] ) ) {
				continue;
			}

			$type          = $field_types[ $eic_key ] ?? 'string';
			$value         = $this->cast_value( $raw[ $openimmo_key ], $type );
			$value         = $this->apply_filter( $eic_key, $value, 'justimmo' );
			$fields[ $eic_key ] = $value;
		}

		// Add technical meta fields.
		$fields['eic_api_source']    = 'justimmo';
		$fields['eic_api_source_id'] = $ext_id;

		// Collect images.
		$images = $this->extract_images( $xml );

		return new ApiResponse( 'justimmo', $ext_id, $fields, $images );
	}

	/**
	 * Map a Justimmo team/employee XML node to an agent ApiResponse.
	 *
	 * @param \SimpleXMLElement $xml   Parsed XML for the agent.
	 * @param string            $ext_id Justimmo team member ID.
	 * @return ApiResponse
	 */
	public function map_agent( \SimpleXMLElement $xml, string $ext_id ): ApiResponse {
		$fields = [
			'eic_vorname'      => (string) ( $xml->vorname ?? '' ),
			'eic_nachname'     => (string) ( $xml->nachname ?? '' ),
			'eic_email'        => (string) ( $xml->email ?? '' ),
			'eic_telefon'      => (string) ( $xml->tel_zentrale ?? '' ),
			'eic_api_source'   => 'justimmo',
			'eic_api_source_id' => $ext_id,
		];

		return new ApiResponse( 'justimmo', $ext_id, $fields, [], [], 'agent' );
	}

	/**
	 * Flatten SimpleXMLElement into a flat associative array.
	 *
	 * @param \SimpleXMLElement $xml
	 * @return array<string, string>
	 */
	private function flatten_xml( \SimpleXMLElement $xml ): array {
		$result = [];
		foreach ( $xml->children() as $child ) {
			$name = $child->getName();
			// Only store scalars; nested elements will be recursed.
			if ( 0 === $child->count() ) {
				$result[ $name ] = (string) $child;
			} else {
				$result = array_merge( $result, $this->flatten_xml( $child ) );
			}
		}
		// Also grab attributes (e.g. <aktion aktivierung="true">).
		foreach ( $xml->attributes() ?? [] as $attr_name => $attr_value ) {
			$result[ (string) $attr_name ] = (string) $attr_value;
		}
		return $result;
	}

	/**
	 * Extract image URLs from a Justimmo property XML.
	 *
	 * @param \SimpleXMLElement $xml
	 * @return string[]
	 */
	private function extract_images( \SimpleXMLElement $xml ): array {
		$images = [];

		// Justimmo puts images in <anhaenge><anhang><daten><pfad>
		foreach ( $xml->xpath( './/anhang/daten/pfad' ) ?: [] as $path ) {
			$url = (string) $path;
			if ( $url ) {
				$images[] = $url;
			}
		}

		return $images;
	}

	/**
	 * Build a key => type lookup from FieldDefinitions.
	 *
	 * @return array<string, string>
	 */
	private function build_type_map(): array {
		$map = [];
		foreach ( FieldDefinitions::get_property_fields() as $field ) {
			$map[ $field['key'] ] = $field['type'];
		}
		return $map;
	}
}
