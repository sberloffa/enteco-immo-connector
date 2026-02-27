<?php
/**
 * Registers CPT 'eic_agent' for real estate agents/brokers.
 */

declare( strict_types=1 );

namespace Enteco\ImmoConnector\PostTypes;

class AgentPostType {

	public const POST_TYPE = 'eic_agent';

	public function register(): void {
		register_post_type(
			self::POST_TYPE,
			[
				'labels'          => $this->get_labels(),
				'public'          => false,
				'show_ui'         => true,
				'show_in_menu'    => false,
				'show_in_rest'    => true,
				'supports'        => [ 'title' ],
				'capability_type' => 'post',
				'map_meta_cap'    => true,
			]
		);
	}

	private function get_labels(): array {
		return [
			'name'               => __( 'Makler', 'enteco-immo-connector' ),
			'singular_name'      => __( 'Makler', 'enteco-immo-connector' ),
			'add_new_item'       => __( 'Neuen Makler hinzufügen', 'enteco-immo-connector' ),
			'edit_item'          => __( 'Makler bearbeiten', 'enteco-immo-connector' ),
			'not_found'          => __( 'Keine Makler gefunden', 'enteco-immo-connector' ),
			'not_found_in_trash' => __( 'Keine Makler im Papierkorb', 'enteco-immo-connector' ),
		];
	}
}
