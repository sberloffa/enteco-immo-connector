<?php
/**
 * Property Importer – creates/updates eic_property posts.
 *
 * @package Enteco\ImmoConnector\Import
 */

declare(strict_types=1);

namespace Enteco\ImmoConnector\Import;

use Enteco\ImmoConnector\Api\ApiResponse;
use Enteco\ImmoConnector\PostTypes\FieldEngine\NativeFieldEngine;

/**
 * Persists normalized property data as WordPress posts.
 */
final class PropertyImporter {

	private const FREE_LIMIT = 50;

	public function __construct(
		private readonly MediaHandler   $media_handler,
		private readonly ImportDiff     $diff,
	) {}

	/**
	 * Import or update a single property.
	 *
	 * @param ApiResponse $response Normalized property data.
	 * @return int WP post ID (created or updated).
	 * @throws \RuntimeException If the free-tier limit is reached.
	 */
	public function import( ApiResponse $response ): int {
		do_action( 'eic/import/before_save_property', $response );

		$external_id = $response->get_external_id();
		$post_id     = $this->diff->find_post_id( $external_id );

		if ( null === $post_id ) {
			$post_id = $this->create_post( $response );
		} else {
			$this->update_post( $post_id, $response );
		}

		$this->save_meta( $post_id, $response );
		$this->handle_media( $post_id, $response );
		$this->set_taxonomies( $post_id, $response );

		do_action( 'eic/import/after_save_property', $post_id, $response );

		return $post_id;
	}

	/**
	 * Mark a property as removed (set status to draft).
	 *
	 * @param string $external_id Provider external ID.
	 */
	public function mark_removed( string $external_id ): void {
		$post_id = $this->diff->find_post_id( $external_id );
		if ( null === $post_id ) {
			return;
		}
		wp_update_post( [ 'ID' => $post_id, 'post_status' => 'draft' ] );
	}

	/** Create a new eic_property post and return its ID. */
	private function create_post( ApiResponse $response ): int {
		$this->check_free_limit();

		$title   = (string) ( $response->get_field( 'eic_objekttitel' ) ?? '' );
		$content = (string) ( $response->get_field( 'eic_objektbeschreibung' ) ?? '' );

		$post_id = wp_insert_post(
			[
				'post_type'    => 'eic_property',
				'post_status'  => 'publish',
				'post_title'   => $title ?: sprintf(
					/* translators: %s: external property ID */
					__( 'Immobilie %s', 'enteco-immo-connector' ),
					$response->get_external_id()
				),
				'post_content' => $content,
				'meta_input'   => [],
			],
			true
		);

		if ( is_wp_error( $post_id ) ) {
			throw new \RuntimeException( 'wp_insert_post failed: ' . $post_id->get_error_message() );
		}

		return $post_id;
	}

	/** Update title/content of an existing post. */
	private function update_post( int $post_id, ApiResponse $response ): void {
		$title   = (string) ( $response->get_field( 'eic_objekttitel' ) ?? '' );
		$content = (string) ( $response->get_field( 'eic_objektbeschreibung' ) ?? '' );

		$data = [ 'ID' => $post_id ];

		if ( $title ) {
			$data['post_title'] = $title;
		}
		if ( $content ) {
			$data['post_content'] = $content;
		}

		wp_update_post( $data );
	}

	/** Save all eic_ meta fields from the response. */
	private function save_meta( int $post_id, ApiResponse $response ): void {
		$engine = new NativeFieldEngine();

		foreach ( $response->get_fields() as $key => $value ) {
			if ( str_starts_with( $key, 'eic_' ) ) {
				$engine->set_field_value( $post_id, $key, $value );
			}
		}

		// Store content hash for change detection.
		update_post_meta( $post_id, 'eic_import_hash', $response->build_hash() );
	}

	/** Download cover image and store gallery URLs. */
	private function handle_media( int $post_id, ApiResponse $response ): void {
		$images = $response->get_images();

		if ( empty( $images ) ) {
			return;
		}

		$cover = array_shift( $images );
		$this->media_handler->set_cover_image( $post_id, $cover );

		// FREE tier: no gallery. Store the remaining URLs anyway for future PRO use.
		if ( ! empty( $images ) ) {
			$this->media_handler->set_gallery_urls( $post_id, $images );
		}
	}

	/** Set taxonomy terms from normalized fields. */
	private function set_taxonomies( int $post_id, ApiResponse $response ): void {
		// eic_vermarktungsart
		$vermarktungsart = $response->get_field( 'eic_vermarktungsart' );
		if ( $vermarktungsart ) {
			wp_set_object_terms( $post_id, (string) $vermarktungsart, 'eic_vermarktungsart' );
		}

		// eic_nutzungsart
		$nutzungsart = $response->get_field( 'eic_nutzungsart' );
		if ( $nutzungsart ) {
			wp_set_object_terms( $post_id, (string) $nutzungsart, 'eic_nutzungsart' );
		}

		// eic_ort – PLZ + Ort
		$plz = (string) ( $response->get_field( 'eic_plz' ) ?? '' );
		$ort = (string) ( $response->get_field( 'eic_ort' ) ?? '' );
		if ( $plz || $ort ) {
			$term_name = trim( "$plz $ort" );
			wp_set_object_terms( $post_id, $term_name, 'eic_ort' );
		}

		// Feature tags from boolean fields.
		$feature_map = [
			'eic_barrierefrei'  => 'barrierefrei',
			'eic_aufzug'        => 'aufzug',
			'eic_kamin'         => 'kamin',
			'eic_gartennutzung' => 'garten',
			'eic_balkon'        => 'balkon',
			'eic_terrasse'      => 'terrasse',
			'eic_moebliert'     => 'moebliert',
			'eic_klimatisiert'  => 'klimaanlage',
			'eic_swimmingpool'  => 'pool',
		];

		$features = [];
		foreach ( $feature_map as $meta_key => $term ) {
			if ( $response->get_field( $meta_key ) ) {
				$features[] = $term;
			}
		}

		if ( ! empty( $features ) ) {
			wp_set_object_terms( $post_id, $features, 'eic_merkmal' );
		}
	}

	/** Throw if the FREE limit would be exceeded. */
	private function check_free_limit(): void {
		$count = wp_count_posts( 'eic_property' );
		$total = (int) ( $count->publish ?? 0 ) + (int) ( $count->draft ?? 0 );

		if ( $total >= self::FREE_LIMIT ) {
			throw new \RuntimeException(
				sprintf(
					/* translators: %d: free limit */
					__( 'Import gestoppt: Die kostenlose Version ist auf %d Immobilien begrenzt. Bitte upgraden Sie auf PRO.', 'enteco-immo-connector' ),
					self::FREE_LIMIT
				)
			);
		}
	}
}
