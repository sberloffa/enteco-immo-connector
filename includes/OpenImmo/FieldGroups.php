<?php
/**
 * Groups field keys by OpenImmo section for structured admin rendering.
 * PRO extends this with the full OpenImmo 1.2.7c field set.
 */

declare( strict_types=1 );

namespace Enteco\ImmoConnector\OpenImmo;

class FieldGroups {

	/** @return array<string, list<string>> */
	public static function get_groups(): array {
		return [
			'preise'      => [
				'eic_kaufpreis', 'eic_kaltmiete', 'eic_warmmiete', 'eic_waehrung',
			],
			'flaechen'    => [
				'eic_wohnflaeche', 'eic_nutzflaeche', 'eic_grundstuecksflaeche', 'eic_anzahl_zimmer',
			],
			'geo'         => [
				'eic_strasse', 'eic_hausnummer', 'eic_plz', 'eic_ort', 'eic_land', 'eic_lat', 'eic_lng',
			],
			'ausstattung' => [
				'eic_feature_balkon', 'eic_feature_garage', 'eic_feature_garten',
				'eic_feature_aufzug', 'eic_feature_barrierefrei', 'eic_feature_keller',
				'eic_feature_einbaukueche', 'eic_feature_stellplatz',
				'eic_feature_terrasse', 'eic_feature_haustiere',
			],
		];
	}
}
