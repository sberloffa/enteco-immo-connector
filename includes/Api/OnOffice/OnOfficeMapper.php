<?php
/**
 * Maps OnOffice JSON response → normalized ApiResponse.
 *
 * @package Enteco\ImmoConnector\Api\OnOffice
 */

declare(strict_types=1);

namespace Enteco\ImmoConnector\Api\OnOffice;

use Enteco\ImmoConnector\Api\ApiResponse;
use Enteco\ImmoConnector\OpenImmo\Mapper;
use Enteco\ImmoConnector\PostTypes\FieldEngine\FieldDefinitions;

/**
 * Translates OnOffice field names to eic_ meta keys and casts values.
 */
final class OnOfficeMapper extends Mapper {

	/**
	 * OnOffice field name → eic_ meta key.
	 *
	 * @var array<string, string>
	 */
	private const FIELD_MAP = [
		'plz'              => 'eic_plz',
		'ort'              => 'eic_ort',
		'strasse'          => 'eic_strasse',
		'hausnummer'       => 'eic_hausnummer',
		'bundesland'       => 'eic_bundesland',
		'land'             => 'eic_land',
		'breitengrad'      => 'eic_breitengrad',
		'laengengrad'      => 'eic_laengengrad',
		'etage'            => 'eic_etage',
		'kaufpreis'        => 'eic_kaufpreis',
		'kaltmiete'        => 'eic_kaltmiete',
		'warmmiete'        => 'eic_warmmiete',
		'nebenkosten'      => 'eic_nebenkosten',
		'heizkosten'       => 'eic_heizkosten',
		'kaution'          => 'eic_kaution',
		'waehrung'         => 'eic_waehrung',
		'wohnflaeche'      => 'eic_wohnflaeche',
		'nutzflaeche'      => 'eic_nutzflaeche',
		'grundstuecksflaeche' => 'eic_grundstuecksflaeche',
		'anzahl_zimmer'    => 'eic_anzahl_zimmer',
		'objekttitel'      => 'eic_objekttitel',
		'objektbeschreibung' => 'eic_objektbeschreibung',
		'lage'             => 'eic_lage',
		'ausstatt_beschr'  => 'eic_ausstatt_beschr',
		'sonstige_angaben' => 'eic_sonstige_angaben',
		'objektnr_intern'  => 'eic_objektnr_intern',
		'objektnr_extern'  => 'eic_objektnr_extern',
		'geaendert_am'     => 'eic_stand_vom',
		'verfuegbar_ab'    => 'eic_verfuegbar_ab',
		'vermietet'        => 'eic_vermietet',
	];

	/**
	 * Map a single OnOffice estate record to an ApiResponse.
	 *
	 * @param array<string, mixed> $record OnOffice estate element.
	 * @param string               $ext_id OnOffice estate ID.
	 * @return ApiResponse
	 */
	public function map_property( array $record, string $ext_id ): ApiResponse {
		$type_map = $this->build_type_map();
		$fields   = [];

		foreach ( self::FIELD_MAP as $oo_key => $eic_key ) {
			if ( ! array_key_exists( $oo_key, $record ) ) {
				continue;
			}
			$type              = $type_map[ $eic_key ] ?? 'string';
			$value             = $this->cast_value( $record[ $oo_key ], $type );
			$value             = $this->apply_filter( $eic_key, $value, 'onoffice' );
			$fields[ $eic_key ] = $value;
		}

		$fields['eic_api_source']    = 'onoffice';
		$fields['eic_api_source_id'] = $ext_id;

		$images = (array) ( $record['_images'] ?? [] );

		return new ApiResponse( 'onoffice', $ext_id, $fields, $images );
	}

	/**
	 * Map an OnOffice address record to an agent ApiResponse.
	 *
	 * @param array<string, mixed> $record OnOffice address element.
	 * @param string               $ext_id OnOffice address ID.
	 * @return ApiResponse
	 */
	public function map_agent( array $record, string $ext_id ): ApiResponse {
		$fields = [
			'eic_vorname'       => (string) ( $record['Vorname'] ?? '' ),
			'eic_nachname'      => (string) ( $record['Name'] ?? '' ),
			'eic_email'         => (string) ( $record['Email'] ?? '' ),
			'eic_telefon'       => (string) ( $record['Telefon1'] ?? '' ),
			'eic_api_source'    => 'onoffice',
			'eic_api_source_id' => $ext_id,
		];

		return new ApiResponse( 'onoffice', $ext_id, $fields, [], [], 'agent' );
	}

	/** @return array<string, string> */
	private function build_type_map(): array {
		$map = [];
		foreach ( FieldDefinitions::get_property_fields() as $field ) {
			$map[ $field['key'] ] = $field['type'];
		}
		return $map;
	}
}
