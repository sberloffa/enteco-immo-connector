<?php
/**
 * OpenImmo 1.2.7c field-mapping schema definitions.
 *
 * Maps OpenImmo XML element paths to our internal eic_ field keys.
 *
 * @package Enteco\ImmoConnector\OpenImmo
 */

declare(strict_types=1);

namespace Enteco\ImmoConnector\OpenImmo;

/**
 * OpenImmo schema: OpenImmo XML path → eic_ meta key.
 */
final class Schema {

	/**
	 * Mapping: OpenImmo XML element name/path → eic_ meta key.
	 * Used by Mappers to normalize provider data.
	 *
	 * @return array<string, string>
	 */
	public static function get_field_map(): array {
		return [
			// geo
			'plz'               => 'eic_plz',
			'ort'               => 'eic_ort',
			'strasse'           => 'eic_strasse',
			'hausnummer'        => 'eic_hausnummer',
			'bundesland'        => 'eic_bundesland',
			'land'              => 'eic_land',
			'breitengrad'       => 'eic_breitengrad',
			'laengengrad'       => 'eic_laengengrad',
			'etage'             => 'eic_etage',
			'anzahl_etagen'     => 'eic_anzahl_etagen',
			'wohnungsnr'        => 'eic_wohnungsnr',

			// preise
			'kaufpreis'         => 'eic_kaufpreis',
			'kaltmiete'         => 'eic_kaltmiete',
			'warmmiete'         => 'eic_warmmiete',
			'nebenkosten'       => 'eic_nebenkosten',
			'heizkosten'        => 'eic_heizkosten',
			'heizkosten_enthalten' => 'eic_heizkosten_enthalten',
			'kaution'           => 'eic_kaution',
			'kaution_text'      => 'eic_kaution_text',
			'provisionspflichtig' => 'eic_provisionspflichtig',
			'courtage_hinweis'  => 'eic_courtage_hinweis',
			'waehrung'          => 'eic_waehrung',
			'freitext_preis'    => 'eic_freitext_preis',

			// flaechen
			'wohnflaeche'       => 'eic_wohnflaeche',
			'nutzflaeche'       => 'eic_nutzflaeche',
			'grundstuecksflaeche' => 'eic_grundstuecksflaeche',
			'gesamtflaeche'     => 'eic_gesamtflaeche',
			'anzahl_zimmer'     => 'eic_anzahl_zimmer',
			'anzahl_schlafzimmer' => 'eic_anzahl_schlafzimmer',
			'anzahl_badezimmer' => 'eic_anzahl_badezimmer',
			'anzahl_sep_wc'     => 'eic_anzahl_sep_wc',
			'anzahl_balkone'    => 'eic_anzahl_balkone',
			'anzahl_terrassen'  => 'eic_anzahl_terrassen',

			// ausstattung
			'ausstatt_kategorie' => 'eic_ausstatt_kategorie',
			'barrierefrei'      => 'eic_barrierefrei',
			'aufzug'            => 'eic_aufzug',
			'kamin'             => 'eic_kamin',
			'gartennutzung'     => 'eic_gartennutzung',
			'balkon'            => 'eic_balkon',
			'terrasse'          => 'eic_terrasse',
			'moebliert'         => 'eic_moebliert',
			'klimatisiert'      => 'eic_klimatisiert',
			'swimmingpool'      => 'eic_swimmingpool',

			// freitexte
			'objekttitel'       => 'eic_objekttitel',
			'objektbeschreibung' => 'eic_objektbeschreibung',
			'lage'              => 'eic_lage',
			'ausstatt_beschr'   => 'eic_ausstatt_beschr',
			'sonstige_angaben'  => 'eic_sonstige_angaben',
			'dreizeiler'        => 'eic_dreizeiler',

			// verwaltung_techn
			'objektnr_intern'   => 'eic_objektnr_intern',
			'objektnr_extern'   => 'eic_objektnr_extern',
			'openimmo_obid'     => 'eic_openimmo_obid',
			'aktion'            => 'eic_aktion',
			'stand_vom'         => 'eic_stand_vom',

			// verwaltung_objekt
			'verfuegbar_ab'     => 'eic_verfuegbar_ab',
			'vermietet'         => 'eic_vermietet',
			'denkmalgeschuetzt' => 'eic_denkmalgeschuetzt',
			'haustiere'         => 'eic_haustiere',
			'nichtraucher'      => 'eic_nichtraucher',
		];
	}

	/**
	 * Reverse map: eic_ meta key → OpenImmo element name.
	 *
	 * @return array<string, string>
	 */
	public static function get_reverse_map(): array {
		return array_flip( self::get_field_map() );
	}
}
