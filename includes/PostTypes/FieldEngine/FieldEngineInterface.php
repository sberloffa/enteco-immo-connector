<?php
/**
 * Contract for all field storage backends (Native / ACF / MetaBox in PRO).
 */

declare( strict_types=1 );

namespace Enteco\ImmoConnector\PostTypes\FieldEngine;

interface FieldEngineInterface {

	/** Register field groups/meta boxes with WordPress. */
	public function register_fields(): void;

	/** Read a single field value for a post. */
	public function get_field_value( int $post_id, string $field_key ): mixed;

	/** Write a single field value for a post. Returns true on success. */
	public function set_field_value( int $post_id, string $field_key, mixed $value ): bool;

	/** Return all defined field values for a post as an associative array. */
	public function get_all_values( int $post_id ): array;
}
