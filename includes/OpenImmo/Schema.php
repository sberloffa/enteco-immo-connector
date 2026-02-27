<?php
/**
 * Defines the canonical normalized property / agent data structure.
 * All provider mappers must return arrays conforming to this schema.
 */

declare( strict_types=1 );

namespace Enteco\ImmoConnector\OpenImmo;

class Schema {

	/**
	 * Returns an empty normalized property array with typed defaults.
	 *
	 * @return array<string, mixed>
	 */
	public static function empty_property(): array {
		return [
			// Identity
			'api_source'          => '',
			'api_source_id'       => '',
			'objektnr_extern'     => '',

			// Content
			'title'               => '',
			'description'         => '',

			// Classification
			'vermarktungsart'     => '', // kauf | miete
			'objektart'           => '',
			'nutzungsart'         => '',
			'zustand'             => '',

			// Pricing
			'kaufpreis'           => null,
			'kaltmiete'           => null,
			'warmmiete'           => null,
			'waehrung'            => 'EUR',

			// Areas
			'wohnflaeche'         => null,
			'nutzflaeche'         => null,
			'grundstuecksflaeche' => null,
			'anzahl_zimmer'       => null,

			// Address
			'strasse'             => '',
			'hausnummer'          => '',
			'plz'                 => '',
			'ort'                 => '',
			'land'                => '',

			// Geo
			'lat'                 => null,
			'lng'                 => null,

			// Boolean features (10 core)
			'features'            => [
				'balkon'          => false,
				'garage'          => false,
				'garten'          => false,
				'aufzug'          => false,
				'barrierefrei'    => false,
				'keller'          => false,
				'einbaukueche'    => false,
				'stellplatz'      => false,
				'terrasse'        => false,
				'haustiere'       => false,
			],

			// Media
			'titelbild_url'       => '',

			// Agent relation
			'agent_api_source'    => '',
			'agent_api_source_id' => '',
		];
	}

	/**
	 * Returns an empty normalized agent array with typed defaults.
	 *
	 * @return array<string, mixed>
	 */
	public static function empty_agent(): array {
		return [
			'api_source'    => '',
			'api_source_id' => '',
			'name'          => '',
			'email'         => '',
			'telefon'       => '',
		];
	}
}
