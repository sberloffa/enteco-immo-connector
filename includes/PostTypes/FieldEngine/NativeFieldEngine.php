<?php
/**
 * Stores and retrieves field values via WordPress postmeta — no 3rd-party dependency.
 */

declare( strict_types=1 );

namespace Enteco\ImmoConnector\PostTypes\FieldEngine;

class NativeFieldEngine implements FieldEngineInterface {

	public function register_fields(): void {
		// Native engine: no explicit registration needed.
		// Meta box UI is rendered by Admin views if required.
	}

	public function get_field_value( int $post_id, string $field_key ): mixed {
		return get_post_meta( $post_id, $field_key, true );
	}

	public function set_field_value( int $post_id, string $field_key, mixed $value ): bool {
		return (bool) update_post_meta( $post_id, $field_key, $value );
	}

	public function get_all_values( int $post_id ): array {
		$values = [];
		foreach ( array_keys( FieldDefinitions::get_all() ) as $key ) {
			$values[ $key ] = $this->get_field_value( $post_id, $key );
		}
		return $values;
	}
}
