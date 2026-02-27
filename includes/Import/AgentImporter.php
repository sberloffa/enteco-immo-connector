<?php
/**
 * Upserts a normalized agent record into WordPress as eic_agent post.
 */

declare( strict_types=1 );

namespace Enteco\ImmoConnector\Import;

use Enteco\ImmoConnector\PostTypes\AgentPostType;
use Enteco\ImmoConnector\PostTypes\FieldEngine\NativeFieldEngine;

class AgentImporter {

	private NativeFieldEngine $engine;

	public function __construct() {
		$this->engine = new NativeFieldEngine();
	}

	/**
	 * @param array<string, mixed> $data Normalized agent data from Mapper.
	 * @return int Post ID, or 0 on failure.
	 */
	public function upsert( array $data ): int {
		$post_id = $this->find_existing( $data['api_source'], $data['api_source_id'] );

		$post_args = [
			'post_type'   => AgentPostType::POST_TYPE,
			'post_title'  => sanitize_text_field( $data['name'] ),
			'post_status' => 'publish',
		];

		if ( $post_id > 0 ) {
			$post_args['ID'] = $post_id;
			$result          = wp_update_post( $post_args, true );
		} else {
			$result = wp_insert_post( $post_args, true );
		}

		if ( is_wp_error( $result ) ) {
			return 0;
		}

		$post_id = (int) $result;

		$this->engine->set_field_value( $post_id, 'eic_agent_api_source',    sanitize_key( $data['api_source'] ) );
		$this->engine->set_field_value( $post_id, 'eic_agent_api_source_id', sanitize_text_field( $data['api_source_id'] ) );
		$this->engine->set_field_value( $post_id, 'eic_agent_name',          sanitize_text_field( $data['name'] ) );
		$this->engine->set_field_value( $post_id, 'eic_agent_email',         sanitize_email( $data['email'] ) );
		$this->engine->set_field_value( $post_id, 'eic_agent_telefon',       sanitize_text_field( $data['telefon'] ) );

		return $post_id;
	}

	private function find_existing( string $api_source, string $api_source_id ): int {
		if ( empty( $api_source ) || empty( $api_source_id ) ) {
			return 0;
		}

		$query = new \WP_Query( [
			'post_type'      => AgentPostType::POST_TYPE,
			'post_status'    => [ 'publish', 'draft' ],
			'posts_per_page' => 1,
			'fields'         => 'ids',
			'meta_query'     => [
				'relation' => 'AND',
				[
					'key'   => 'eic_agent_api_source',
					'value' => $api_source,
				],
				[
					'key'   => 'eic_agent_api_source_id',
					'value' => $api_source_id,
				],
			],
		] );

		$posts = $query->posts;
		wp_reset_postdata();

		return ! empty( $posts ) ? (int) $posts[0] : 0;
	}
}
