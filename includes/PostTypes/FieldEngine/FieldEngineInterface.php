<?php
/**
 * Contract for all Field Engine implementations.
 *
 * @package Enteco\ImmoConnector\PostTypes\FieldEngine
 */

declare(strict_types=1);

namespace Enteco\ImmoConnector\PostTypes\FieldEngine;

/**
 * All field engines (Native, ACF, MetaBox) must implement this interface.
 */
interface FieldEngineInterface {

	/** Register field definitions with the chosen engine. */
	public function register_fields(): void;

	/**
	 * Get a single field value.
	 *
	 * @param int    $post_id   The post/property ID.
	 * @param string $field_key The field key (e.g. 'eic_kaufpreis').
	 * @return mixed
	 */
	public function get_field_value( int $post_id, string $field_key ): mixed;

	/**
	 * Set a single field value.
	 *
	 * @param int    $post_id   The post/property ID.
	 * @param string $field_key The field key.
	 * @param mixed  $value     The value to store.
	 * @return bool True on success.
	 */
	public function set_field_value( int $post_id, string $field_key, mixed $value ): bool;

	/**
	 * Get all field values for a post.
	 *
	 * @param int $post_id The post/property ID.
	 * @return array<string, mixed> Associative array of field_key => value.
	 */
	public function get_all_values( int $post_id ): array;
}
