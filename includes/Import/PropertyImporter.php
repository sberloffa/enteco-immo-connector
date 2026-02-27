<?php
/**
 * Upserts a single normalized property into WordPress as eic_property post.
 */

declare( strict_types=1 );

namespace Enteco\ImmoConnector\Import;

use Enteco\ImmoConnector\PostTypes\FieldEngine\FieldDefinitions;
use Enteco\ImmoConnector\PostTypes\FieldEngine\NativeFieldEngine;
use Enteco\ImmoConnector\PostTypes\PropertyPostType;

class PropertyImporter {

	private NativeFieldEngine $engine;

	public function __construct() {
		$this->engine = new NativeFieldEngine();
	}

	/**
	 * @param array<string, mixed> $data Normalized data from Mapper.
	 * @return bool True on success, false on failure.
	 */
	public function upsert( array $data ): bool {
		$post_id = $this->find_existing( $data['api_source'], $data['api_source_id'] );

		$post_args = [
			'post_type'    => PropertyPostType::POST_TYPE,
			'post_title'   => sanitize_text_field( $data['title'] ),
			'post_content' => wp_kses_post( $data['description'] ),
			'post_status'  => 'publish',
		];

		if ( $post_id > 0 ) {
			$post_args['ID'] = $post_id;
			$result          = wp_update_post( $post_args, true );
		} else {
			$result = wp_insert_post( $post_args, true );
		}

		if ( is_wp_error( $result ) ) {
			return false;
		}

		$post_id = (int) $result;

		$this->save_meta( $post_id, $data );
		$this->set_taxonomies( $post_id, $data );

		if ( ! empty( $data['titelbild_url'] ) ) {
			( new MediaHandler() )->set_thumbnail(
				$post_id,
				$data['titelbild_url'],
				$data['title']
			);
		}

		return true;
	}

	/** @param array<string, mixed> $data */
	private function save_meta( int $post_id, array $data ): void {
		$field_map = [
			'eic_api_source'          => $data['api_source'],
			'eic_api_source_id'       => $data['api_source_id'],
			'eic_objektnr_extern'     => $data['objektnr_extern'],
			'eic_kaufpreis'           => $data['kaufpreis'],
			'eic_kaltmiete'           => $data['kaltmiete'],
			'eic_warmmiete'           => $data['warmmiete'],
			'eic_waehrung'            => $data['waehrung'],
			'eic_wohnflaeche'         => $data['wohnflaeche'],
			'eic_nutzflaeche'         => $data['nutzflaeche'],
			'eic_grundstuecksflaeche' => $data['grundstuecksflaeche'],
			'eic_anzahl_zimmer'       => $data['anzahl_zimmer'],
			'eic_strasse'             => $data['strasse'],
			'eic_hausnummer'          => $data['hausnummer'],
			'eic_plz'                 => $data['plz'],
			'eic_ort'                 => $data['ort'],
			'eic_land'                => $data['land'],
			'eic_lat'                 => $data['lat'],
			'eic_lng'                 => $data['lng'],
		];

		foreach ( ( $data['features'] ?? [] ) as $key => $value ) {
			$field_map[ 'eic_feature_' . $key ] = $value ? '1' : '0';
		}

		$definitions = FieldDefinitions::get_all();
		foreach ( $field_map as $meta_key => $value ) {
			if ( $value === null ) {
				continue;
			}
			$sanitize = $definitions[ $meta_key ]['sanitize'] ?? 'sanitize_text_field';
			if ( is_callable( $sanitize ) ) {
				$value = $sanitize( $value );
			}
			$this->engine->set_field_value( $post_id, $meta_key, $value );
		}
	}

	/** @param array<string, mixed> $data */
	private function set_taxonomies( int $post_id, array $data ): void {
		$taxonomy_map = [
			'eic_vermarktungsart' => $data['vermarktungsart'] ?? '',
			'eic_objektart'       => $data['objektart']       ?? '',
			'eic_nutzungsart'     => $data['nutzungsart']     ?? '',
			'eic_zustand'         => $data['zustand']         ?? '',
		];

		foreach ( $taxonomy_map as $taxonomy => $term ) {
			if ( ! empty( $term ) ) {
				wp_set_object_terms( $post_id, sanitize_text_field( $term ), $taxonomy );
			}
		}
	}

	private function find_existing( string $api_source, string $api_source_id ): int {
		if ( empty( $api_source ) || empty( $api_source_id ) ) {
			return 0;
		}

		$query = new \WP_Query( [
			'post_type'      => PropertyPostType::POST_TYPE,
			'post_status'    => [ 'publish', 'draft' ],
			'posts_per_page' => 1,
			'fields'         => 'ids',
			'meta_query'     => [
				'relation' => 'AND',
				[
					'key'   => 'eic_api_source',
					'value' => $api_source,
				],
				[
					'key'   => 'eic_api_source_id',
					'value' => $api_source_id,
				],
			],
		] );

		$posts = $query->posts;
		wp_reset_postdata();

		return ! empty( $posts ) ? (int) $posts[0] : 0;
	}
}
