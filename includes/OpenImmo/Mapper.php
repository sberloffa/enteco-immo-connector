<?php
/**
 * Base mapper: generic API response → normalized OpenImmo array.
 *
 * @package Enteco\ImmoConnector\OpenImmo
 */

declare(strict_types=1);

namespace Enteco\ImmoConnector\OpenImmo;

/**
 * Provides helper methods shared across provider-specific mappers.
 */
abstract class Mapper {

	/**
	 * Normalize a raw value according to the field type.
	 *
	 * @param mixed  $value Raw value from provider.
	 * @param string $type  Field type (float, int, bool, string…).
	 * @return mixed
	 */
	protected function cast_value( mixed $value, string $type ): mixed {
		if ( null === $value || '' === $value ) {
			return null;
		}

		return match ( $type ) {
			'float'                  => (float) str_replace( ',', '.', (string) $value ),
			'int'                    => (int) $value,
			'bool'                   => $this->cast_bool( $value ),
			'string', 'select',
			'email', 'url',
			'date', 'datetime',
			'textarea'               => (string) $value,
			'multiselect'            => is_array( $value ) ? $value : [ (string) $value ],
			default                  => $value,
		};
	}

	/**
	 * Convert various truthy/falsy representations to bool.
	 *
	 * @param mixed $value Raw value.
	 */
	protected function cast_bool( mixed $value ): bool {
		if ( is_bool( $value ) ) {
			return $value;
		}
		$lower = strtolower( (string) $value );
		return in_array( $lower, [ '1', 'true', 'yes', 'ja', 'x' ], true );
	}

	/**
	 * Apply the eic/mapper/field_value filter to allow external customization.
	 *
	 * @param string $key   eic_ meta key.
	 * @param mixed  $value Normalized value.
	 * @param string $source Provider name (justimmo|onoffice).
	 * @return mixed
	 */
	protected function apply_filter( string $key, mixed $value, string $source ): mixed {
		return apply_filters( 'eic/mapper/field_value', $value, $key, $source );
	}
}
