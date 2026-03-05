<?php
/**
 * Normalized API response value object.
 *
 * @package Enteco\ImmoConnector\Api
 */

declare(strict_types=1);

namespace Enteco\ImmoConnector\Api;

/**
 * Immutable container for a single normalized property or agent record.
 */
final class ApiResponse {

	/**
	 * @param string                $source      Provider slug (justimmo|onoffice).
	 * @param string                $external_id Provider's own ID for the record.
	 * @param array<string, mixed>  $fields      Normalized eic_ meta fields.
	 * @param array<string, string> $images      Array of image URLs (first = cover).
	 * @param array<string, string> $documents   Array of document URLs.
	 * @param string                $type        'property' or 'agent'.
	 */
	public function __construct(
		private readonly string $source,
		private readonly string $external_id,
		private readonly array  $fields    = [],
		private readonly array  $images    = [],
		private readonly array  $documents = [],
		private readonly string $type      = 'property',
	) {}

	public function get_source(): string {
		return $this->source;
	}

	public function get_external_id(): string {
		return $this->external_id;
	}

	/** @return array<string, mixed> */
	public function get_fields(): array {
		return $this->fields;
	}

	/** @return array<string, string> */
	public function get_images(): array {
		return $this->images;
	}

	/** @return array<string, string> */
	public function get_documents(): array {
		return $this->documents;
	}

	public function get_type(): string {
		return $this->type;
	}

	/**
	 * Get a single field value.
	 *
	 * @param string $key eic_ meta key.
	 * @return mixed
	 */
	public function get_field( string $key ): mixed {
		return $this->fields[ $key ] ?? null;
	}

	/** Return the cover (first) image URL, or empty string. */
	public function get_cover_image(): string {
		return $this->images[0] ?? '';
	}

	/**
	 * Build a content hash for change detection.
	 */
	public function build_hash(): string {
		return md5( serialize( $this->fields ) );
	}

	/**
	 * Return a copy with additional or overridden fields.
	 *
	 * @param array<string, mixed> $extra_fields
	 */
	public function with_fields( array $extra_fields ): self {
		return new self(
			$this->source,
			$this->external_id,
			array_merge( $this->fields, $extra_fields ),
			$this->images,
			$this->documents,
			$this->type,
		);
	}
}
