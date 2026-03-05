<?php
/**
 * Field Definitions – Single Source of Truth for FREE field set.
 *
 * Defines all fields registered for eic_property and eic_agent posts
 * (FREE tier: geo, preise, flaechen + 10 core ausstattung fields).
 *
 * @package Enteco\ImmoConnector\PostTypes\FieldEngine
 */

declare(strict_types=1);

namespace Enteco\ImmoConnector\PostTypes\FieldEngine;

/**
 * Centralized field definitions consumed by all FieldEngine implementations.
 */
final class FieldDefinitions {

	/**
	 * Return all property field definitions.
	 *
	 * Each definition is an associative array with keys:
	 *   key    (string)  – the meta key, prefixed eic_
	 *   type   (string)  – 'string'|'float'|'int'|'bool'|'select'|'multiselect'|'email'|'url'|'date'|'datetime'|'textarea'
	 *   label  (string)  – Human-readable label (already translated via __())
	 *   group  (string)  – logical group: geo|preise|flaechen|ausstattung|freitexte|verwaltung_techn|verwaltung_objekt
	 *   options (array)  – for select/multiselect types
	 *   default (mixed)  – default value (optional)
	 *
	 * @return array<int, array<string, mixed>>
	 */
	public static function get_property_fields(): array {
		return array_merge(
			self::geo_fields(),
			self::preise_fields(),
			self::flaechen_fields(),
			self::ausstattung_fields(),
			self::freitexte_fields(),
			self::verwaltung_techn_fields(),
			self::verwaltung_objekt_fields()
		);
	}

	/**
	 * Return agent field definitions.
	 *
	 * @return array<int, array<string, mixed>>
	 */
	public static function get_agent_fields(): array {
		return [
			[ 'key' => 'eic_anrede',      'type' => 'select', 'label' => 'Anrede',     'group' => 'agent', 'options' => [ 'herr' => 'Herr', 'frau' => 'Frau', 'divers' => 'Divers' ] ],
			[ 'key' => 'eic_vorname',     'type' => 'string', 'label' => 'Vorname',     'group' => 'agent' ],
			[ 'key' => 'eic_nachname',    'type' => 'string', 'label' => 'Nachname',    'group' => 'agent' ],
			[ 'key' => 'eic_email',       'type' => 'email',  'label' => 'E-Mail',      'group' => 'agent' ],
			[ 'key' => 'eic_telefon',     'type' => 'string', 'label' => 'Telefon',     'group' => 'agent' ],
			[ 'key' => 'eic_api_source',  'type' => 'string', 'label' => 'API Source',  'group' => 'agent' ],
			[ 'key' => 'eic_api_source_id', 'type' => 'string', 'label' => 'API Source ID', 'group' => 'agent' ],
		];
	}

	// -------------------------------------------------------------------------
	// Field groups
	// -------------------------------------------------------------------------

	/** @return array<int, array<string, mixed>> */
	private static function geo_fields(): array {
		return [
			[ 'key' => 'eic_plz',          'type' => 'string', 'label' => 'PLZ',           'group' => 'geo' ],
			[ 'key' => 'eic_ort',          'type' => 'string', 'label' => 'Ort',            'group' => 'geo' ],
			[ 'key' => 'eic_strasse',      'type' => 'string', 'label' => 'Straße',         'group' => 'geo' ],
			[ 'key' => 'eic_hausnummer',   'type' => 'string', 'label' => 'Hausnummer',     'group' => 'geo' ],
			[ 'key' => 'eic_bundesland',   'type' => 'string', 'label' => 'Bundesland',     'group' => 'geo' ],
			[ 'key' => 'eic_land',         'type' => 'string', 'label' => 'Land (ISO)',     'group' => 'geo' ],
			[ 'key' => 'eic_breitengrad',  'type' => 'float',  'label' => 'Breitengrad',   'group' => 'geo' ],
			[ 'key' => 'eic_laengengrad',  'type' => 'float',  'label' => 'Längengrad',    'group' => 'geo' ],
			[ 'key' => 'eic_etage',        'type' => 'int',    'label' => 'Etage',         'group' => 'geo' ],
			[ 'key' => 'eic_anzahl_etagen','type' => 'int',    'label' => 'Anzahl Etagen', 'group' => 'geo' ],
			[ 'key' => 'eic_wohnungsnr',   'type' => 'string', 'label' => 'Wohnungsnr.',   'group' => 'geo' ],
		];
	}

	/** @return array<int, array<string, mixed>> */
	private static function preise_fields(): array {
		return [
			[ 'key' => 'eic_kaufpreis',          'type' => 'float',  'label' => 'Kaufpreis',         'group' => 'preise' ],
			[ 'key' => 'eic_kaltmiete',           'type' => 'float',  'label' => 'Kaltmiete',         'group' => 'preise' ],
			[ 'key' => 'eic_warmmiete',           'type' => 'float',  'label' => 'Warmmiete',         'group' => 'preise' ],
			[ 'key' => 'eic_nebenkosten',         'type' => 'float',  'label' => 'Nebenkosten',       'group' => 'preise' ],
			[ 'key' => 'eic_heizkosten',          'type' => 'float',  'label' => 'Heizkosten',        'group' => 'preise' ],
			[ 'key' => 'eic_heizkosten_enthalten','type' => 'bool',   'label' => 'Heizkosten inkl.', 'group' => 'preise' ],
			[ 'key' => 'eic_kaution',             'type' => 'float',  'label' => 'Kaution',           'group' => 'preise' ],
			[ 'key' => 'eic_kaution_text',        'type' => 'string', 'label' => 'Kaution (Text)',    'group' => 'preise' ],
			[ 'key' => 'eic_provisionspflichtig', 'type' => 'bool',   'label' => 'Provisionspflichtig', 'group' => 'preise' ],
			[ 'key' => 'eic_courtage_hinweis',    'type' => 'string', 'label' => 'Courtage-Hinweis',  'group' => 'preise' ],
			[ 'key' => 'eic_waehrung',            'type' => 'string', 'label' => 'Währung',           'group' => 'preise', 'default' => 'EUR' ],
			[ 'key' => 'eic_freitext_preis',      'type' => 'string', 'label' => 'Preis (Freitext)',  'group' => 'preise' ],
		];
	}

	/** @return array<int, array<string, mixed>> */
	private static function flaechen_fields(): array {
		return [
			[ 'key' => 'eic_wohnflaeche',        'type' => 'float', 'label' => 'Wohnfläche (m²)',       'group' => 'flaechen' ],
			[ 'key' => 'eic_nutzflaeche',        'type' => 'float', 'label' => 'Nutzfläche (m²)',       'group' => 'flaechen' ],
			[ 'key' => 'eic_grundstuecksflaeche','type' => 'float', 'label' => 'Grundstücksfläche (m²)','group' => 'flaechen' ],
			[ 'key' => 'eic_gesamtflaeche',      'type' => 'float', 'label' => 'Gesamtfläche (m²)',     'group' => 'flaechen' ],
			[ 'key' => 'eic_anzahl_zimmer',      'type' => 'float', 'label' => 'Zimmer',                'group' => 'flaechen' ],
			[ 'key' => 'eic_anzahl_schlafzimmer','type' => 'float', 'label' => 'Schlafzimmer',          'group' => 'flaechen' ],
			[ 'key' => 'eic_anzahl_badezimmer',  'type' => 'float', 'label' => 'Badezimmer',            'group' => 'flaechen' ],
			[ 'key' => 'eic_anzahl_sep_wc',      'type' => 'int',   'label' => 'Sep. WC',              'group' => 'flaechen' ],
			[ 'key' => 'eic_anzahl_balkone',     'type' => 'int',   'label' => 'Balkone',              'group' => 'flaechen' ],
			[ 'key' => 'eic_anzahl_terrassen',   'type' => 'int',   'label' => 'Terrassen',            'group' => 'flaechen' ],
		];
	}

	/** Core 10 ausstattung fields for FREE tier. */
	/** @return array<int, array<string, mixed>> */
	private static function ausstattung_fields(): array {
		return [
			[
				'key'     => 'eic_ausstatt_kategorie',
				'type'    => 'select',
				'label'   => 'Ausstattungskategorie',
				'group'   => 'ausstattung',
				'options' => [ 'STANDARD' => 'Standard', 'GEHOBEN' => 'Gehoben', 'LUXUS' => 'Luxus' ],
			],
			[ 'key' => 'eic_barrierefrei',    'type' => 'bool', 'label' => 'Barrierefrei',    'group' => 'ausstattung' ],
			[ 'key' => 'eic_aufzug',          'type' => 'bool', 'label' => 'Aufzug',           'group' => 'ausstattung' ],
			[ 'key' => 'eic_kamin',           'type' => 'bool', 'label' => 'Kamin',            'group' => 'ausstattung' ],
			[ 'key' => 'eic_gartennutzung',   'type' => 'bool', 'label' => 'Gartennutzung',    'group' => 'ausstattung' ],
			[ 'key' => 'eic_balkon',          'type' => 'bool', 'label' => 'Balkon',           'group' => 'ausstattung' ],
			[ 'key' => 'eic_terrasse',        'type' => 'bool', 'label' => 'Terrasse',         'group' => 'ausstattung' ],
			[ 'key' => 'eic_moebliert',       'type' => 'bool', 'label' => 'Möbliert',         'group' => 'ausstattung' ],
			[ 'key' => 'eic_klimatisiert',    'type' => 'bool', 'label' => 'Klimaanlage',      'group' => 'ausstattung' ],
			[ 'key' => 'eic_swimmingpool',    'type' => 'bool', 'label' => 'Schwimmbad/Pool',  'group' => 'ausstattung' ],
		];
	}

	/** @return array<int, array<string, mixed>> */
	private static function freitexte_fields(): array {
		return [
			[ 'key' => 'eic_objekttitel',         'type' => 'string',   'label' => 'Objekttitel',           'group' => 'freitexte' ],
			[ 'key' => 'eic_objektbeschreibung',  'type' => 'textarea', 'label' => 'Objektbeschreibung',    'group' => 'freitexte' ],
			[ 'key' => 'eic_lage',                'type' => 'textarea', 'label' => 'Lagebeschreibung',      'group' => 'freitexte' ],
			[ 'key' => 'eic_ausstatt_beschr',     'type' => 'textarea', 'label' => 'Ausstattungsbeschreibung', 'group' => 'freitexte' ],
			[ 'key' => 'eic_sonstige_angaben',    'type' => 'textarea', 'label' => 'Sonstige Angaben',      'group' => 'freitexte' ],
			[ 'key' => 'eic_dreizeiler',          'type' => 'string',   'label' => 'Dreizeiler',            'group' => 'freitexte' ],
		];
	}

	/** @return array<int, array<string, mixed>> */
	private static function verwaltung_techn_fields(): array {
		return [
			[ 'key' => 'eic_objektnr_intern',  'type' => 'string',   'label' => 'Objektnr. intern',  'group' => 'verwaltung_techn' ],
			[ 'key' => 'eic_objektnr_extern',  'type' => 'string',   'label' => 'Objektnr. extern',  'group' => 'verwaltung_techn' ],
			[ 'key' => 'eic_openimmo_obid',    'type' => 'string',   'label' => 'OpenImmo OBID',     'group' => 'verwaltung_techn' ],
			[ 'key' => 'eic_aktion',           'type' => 'select',   'label' => 'Aktion',            'group' => 'verwaltung_techn', 'options' => [ 'ACTIVE' => 'Aktiv', 'DELETE' => 'Löschen' ] ],
			[ 'key' => 'eic_stand_vom',        'type' => 'datetime', 'label' => 'Stand vom',         'group' => 'verwaltung_techn' ],
			[ 'key' => 'eic_api_source',       'type' => 'string',   'label' => 'API Source',        'group' => 'verwaltung_techn' ],
			[ 'key' => 'eic_api_source_id',    'type' => 'string',   'label' => 'API Source ID',     'group' => 'verwaltung_techn' ],
			[ 'key' => 'eic_import_hash',      'type' => 'string',   'label' => 'Import Hash',       'group' => 'verwaltung_techn' ],
		];
	}

	/** @return array<int, array<string, mixed>> */
	private static function verwaltung_objekt_fields(): array {
		return [
			[ 'key' => 'eic_verfuegbar_ab',    'type' => 'string', 'label' => 'Verfügbar ab',      'group' => 'verwaltung_objekt' ],
			[ 'key' => 'eic_vermietet',        'type' => 'bool',   'label' => 'Vermietet',          'group' => 'verwaltung_objekt' ],
			[ 'key' => 'eic_denkmalgeschuetzt','type' => 'bool',   'label' => 'Denkmalgeschützt',   'group' => 'verwaltung_objekt' ],
			[ 'key' => 'eic_haustiere',        'type' => 'bool',   'label' => 'Haustiere erlaubt',  'group' => 'verwaltung_objekt' ],
			[ 'key' => 'eic_nichtraucher',     'type' => 'bool',   'label' => 'Nichtraucher',       'group' => 'verwaltung_objekt' ],
		];
	}

	/**
	 * Get a field definition by key.
	 *
	 * @param string $key The meta key to look up.
	 * @return array<string, mixed>|null
	 */
	public static function get_field( string $key ): ?array {
		foreach ( self::get_property_fields() as $field ) {
			if ( $field['key'] === $key ) {
				return $field;
			}
		}
		return null;
	}

	/**
	 * Get all field keys.
	 *
	 * @return string[]
	 */
	public static function get_field_keys(): array {
		return array_column( self::get_property_fields(), 'key' );
	}

	/**
	 * Get all fields for a specific group.
	 *
	 * @param string $group Group name (geo, preise, flaechen, etc.)
	 * @return array<int, array<string, mixed>>
	 */
	public static function get_fields_by_group( string $group ): array {
		return array_values(
			array_filter(
				self::get_property_fields(),
				fn( array $field ) => $field['group'] === $group
			)
		);
	}
}
