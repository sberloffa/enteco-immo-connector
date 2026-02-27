<?php
/**
 * Single source of truth for FREE base field definitions.
 * Keys are the meta_key strings stored in postmeta.
 * PRO extends this with ProFieldDefinitions (full OpenImmo 1.2.7c set).
 */

declare( strict_types=1 );

namespace Enteco\ImmoConnector\PostTypes\FieldEngine;

class FieldDefinitions {

	/**
	 * Returns all property field definitions.
	 *
	 * @return array<string, array{type: string, label: string, sanitize: string}>
	 */
	public static function get_all(): array {
		return [
			// ── Identity ──────────────────────────────────────────────────────
			'eic_api_source'          => [ 'type' => 'string', 'label' => 'API Source',              'sanitize' => 'sanitize_key' ],
			'eic_api_source_id'       => [ 'type' => 'string', 'label' => 'API Source ID',           'sanitize' => 'sanitize_text_field' ],
			'eic_objektnr_extern'     => [ 'type' => 'string', 'label' => 'Externe Objektnummer',    'sanitize' => 'sanitize_text_field' ],

			// ── Pricing ───────────────────────────────────────────────────────
			'eic_kaufpreis'           => [ 'type' => 'float',  'label' => 'Kaufpreis',               'sanitize' => 'floatval' ],
			'eic_kaltmiete'           => [ 'type' => 'float',  'label' => 'Kaltmiete',               'sanitize' => 'floatval' ],
			'eic_warmmiete'           => [ 'type' => 'float',  'label' => 'Warmmiete',               'sanitize' => 'floatval' ],
			'eic_waehrung'            => [ 'type' => 'string', 'label' => 'Währung',                 'sanitize' => 'sanitize_text_field' ],

			// ── Areas ─────────────────────────────────────────────────────────
			'eic_wohnflaeche'         => [ 'type' => 'float',  'label' => 'Wohnfläche m²',           'sanitize' => 'floatval' ],
			'eic_nutzflaeche'         => [ 'type' => 'float',  'label' => 'Nutzfläche m²',           'sanitize' => 'floatval' ],
			'eic_grundstuecksflaeche' => [ 'type' => 'float',  'label' => 'Grundstücksfläche m²',   'sanitize' => 'floatval' ],
			'eic_anzahl_zimmer'       => [ 'type' => 'float',  'label' => 'Anzahl Zimmer',           'sanitize' => 'floatval' ],

			// ── Address ───────────────────────────────────────────────────────
			'eic_strasse'             => [ 'type' => 'string', 'label' => 'Straße',                  'sanitize' => 'sanitize_text_field' ],
			'eic_hausnummer'          => [ 'type' => 'string', 'label' => 'Hausnummer',              'sanitize' => 'sanitize_text_field' ],
			'eic_plz'                 => [ 'type' => 'string', 'label' => 'PLZ',                     'sanitize' => 'sanitize_text_field' ],
			'eic_ort'                 => [ 'type' => 'string', 'label' => 'Ort',                     'sanitize' => 'sanitize_text_field' ],
			'eic_land'                => [ 'type' => 'string', 'label' => 'Land',                    'sanitize' => 'sanitize_text_field' ],

			// ── Geo ───────────────────────────────────────────────────────────
			'eic_lat'                 => [ 'type' => 'float',  'label' => 'Breitengrad',             'sanitize' => 'floatval' ],
			'eic_lng'                 => [ 'type' => 'float',  'label' => 'Längengrad',              'sanitize' => 'floatval' ],

			// ── Boolean features (10 core) ────────────────────────────────────
			'eic_feature_balkon'      => [ 'type' => 'bool',   'label' => 'Balkon',                  'sanitize' => 'absint' ],
			'eic_feature_garage'      => [ 'type' => 'bool',   'label' => 'Garage',                  'sanitize' => 'absint' ],
			'eic_feature_garten'      => [ 'type' => 'bool',   'label' => 'Garten',                  'sanitize' => 'absint' ],
			'eic_feature_aufzug'      => [ 'type' => 'bool',   'label' => 'Aufzug',                  'sanitize' => 'absint' ],
			'eic_feature_barrierefrei'=> [ 'type' => 'bool',   'label' => 'Barrierefrei',            'sanitize' => 'absint' ],
			'eic_feature_keller'      => [ 'type' => 'bool',   'label' => 'Keller',                  'sanitize' => 'absint' ],
			'eic_feature_einbaukueche'=> [ 'type' => 'bool',   'label' => 'Einbauküche',             'sanitize' => 'absint' ],
			'eic_feature_stellplatz'  => [ 'type' => 'bool',   'label' => 'Stellplatz',              'sanitize' => 'absint' ],
			'eic_feature_terrasse'    => [ 'type' => 'bool',   'label' => 'Terrasse',                'sanitize' => 'absint' ],
			'eic_feature_haustiere'   => [ 'type' => 'bool',   'label' => 'Haustiere erlaubt',       'sanitize' => 'absint' ],
		];
	}

	/**
	 * Returns agent field definitions (FREE subset).
	 *
	 * @return array<string, array{type: string, label: string, sanitize: string}>
	 */
	public static function get_agent_fields(): array {
		return [
			'eic_agent_api_source'    => [ 'type' => 'string', 'label' => 'API Source',    'sanitize' => 'sanitize_key' ],
			'eic_agent_api_source_id' => [ 'type' => 'string', 'label' => 'API Source ID', 'sanitize' => 'sanitize_text_field' ],
			'eic_agent_name'          => [ 'type' => 'string', 'label' => 'Name',          'sanitize' => 'sanitize_text_field' ],
			'eic_agent_email'         => [ 'type' => 'string', 'label' => 'E-Mail',        'sanitize' => 'sanitize_email' ],
			'eic_agent_telefon'       => [ 'type' => 'string', 'label' => 'Telefon',       'sanitize' => 'sanitize_text_field' ],
		];
	}
}
