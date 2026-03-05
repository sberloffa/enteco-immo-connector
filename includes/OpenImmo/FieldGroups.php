<?php
/**
 * OpenImmo field group definitions.
 *
 * @package Enteco\ImmoConnector\OpenImmo
 */

declare(strict_types=1);

namespace Enteco\ImmoConnector\OpenImmo;

/**
 * Logical grouping of eic_ fields, mirroring OpenImmo structure.
 */
final class FieldGroups {

	public const GEO               = 'geo';
	public const PREISE            = 'preise';
	public const FLAECHEN          = 'flaechen';
	public const AUSSTATTUNG       = 'ausstattung';
	public const FREITEXTE         = 'freitexte';
	public const VERWALTUNG_TECHN  = 'verwaltung_techn';
	public const VERWALTUNG_OBJEKT = 'verwaltung_objekt';

	/** All group names. */
	public const ALL_GROUPS = [
		self::GEO,
		self::PREISE,
		self::FLAECHEN,
		self::AUSSTATTUNG,
		self::FREITEXTE,
		self::VERWALTUNG_TECHN,
		self::VERWALTUNG_OBJEKT,
	];

	/**
	 * Return all eic_ keys belonging to a group.
	 *
	 * @param string $group Group constant.
	 * @return string[]
	 */
	public static function get_keys_for_group( string $group ): array {
		return \Enteco\ImmoConnector\PostTypes\FieldEngine\FieldDefinitions::get_fields_by_group( $group ) !== []
			? array_column(
				\Enteco\ImmoConnector\PostTypes\FieldEngine\FieldDefinitions::get_fields_by_group( $group ),
				'key'
			)
			: [];
	}
}
