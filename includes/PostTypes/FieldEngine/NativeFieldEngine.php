<?php
/**
 * Native WordPress Field Engine – uses postmeta table.
 *
 * @package Enteco\ImmoConnector\PostTypes\FieldEngine
 */

declare(strict_types=1);

namespace Enteco\ImmoConnector\PostTypes\FieldEngine;

/**
 * Stores and retrieves field values via WP postmeta (get_post_meta / update_post_meta).
 */
final class NativeFieldEngine implements FieldEngineInterface {

	/** Register fields via register_meta() for REST-API visibility. */
	public function register_fields(): void {
		add_action( 'init', [ $this, 'do_register_fields' ] );
	}

	/** register_meta callback. */
	public function do_register_fields(): void {
		foreach ( FieldDefinitions::get_property_fields() as $field ) {
			register_meta(
				'post',
				$field['key'],
				[
					'object_subtype' => 'eic_property',
					'type'           => $this->map_type( $field['type'] ),
					'single'         => true,
					'show_in_rest'   => false,
					'auth_callback'  => fn() => current_user_can( 'edit_posts' ),
				]
			);
		}

		foreach ( FieldDefinitions::get_agent_fields() as $field ) {
			register_meta(
				'post',
				$field['key'],
				[
					'object_subtype' => 'eic_agent',
					'type'           => $this->map_type( $field['type'] ),
					'single'         => true,
					'show_in_rest'   => false,
					'auth_callback'  => fn() => current_user_can( 'edit_posts' ),
				]
			);
		}
	}

	/** {@inheritdoc} */
	public function get_field_value( int $post_id, string $field_key ): mixed {
		return get_post_meta( $post_id, $field_key, true );
	}

	/** {@inheritdoc} */
	public function set_field_value( int $post_id, string $field_key, mixed $value ): bool {
		$result = update_post_meta( $post_id, $field_key, $value );
		return false !== $result;
	}

	/** {@inheritdoc} */
	public function get_all_values( int $post_id ): array {
		$result = [];
		foreach ( FieldDefinitions::get_field_keys() as $key ) {
			$result[ $key ] = get_post_meta( $post_id, $key, true );
		}
		return $result;
	}

	/**
	 * Map internal field type to register_meta schema type.
	 *
	 * @param string $type Internal type string.
	 * @return string WP schema type.
	 */
	private function map_type( string $type ): string {
		return match ( $type ) {
			'int'                    => 'integer',
			'float'                  => 'number',
			'bool'                   => 'boolean',
			'select', 'multiselect',
			'string', 'email', 'url',
			'date', 'datetime',
			'textarea'               => 'string',
			default                  => 'string',
		};
	}
}
