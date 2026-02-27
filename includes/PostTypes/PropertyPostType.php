<?php
/**
 * Registers CPT 'eic_property' for real estate listings.
 */

declare( strict_types=1 );

namespace Enteco\ImmoConnector\PostTypes;

class PropertyPostType {

	public const POST_TYPE = 'eic_property';

	public function register(): void {
		register_post_type(
			self::POST_TYPE,
			[
				'labels'          => $this->get_labels(),
				'public'          => true,
				'show_ui'         => true,
				'show_in_menu'    => false,
				'show_in_rest'    => true,
				'supports'        => [ 'title', 'editor', 'thumbnail', 'excerpt' ],
				'has_archive'     => true,
				'rewrite'         => [ 'slug' => 'immobilien', 'with_front' => false ],
				'menu_icon'       => 'dashicons-building',
				'capability_type' => 'post',
				'map_meta_cap'    => true,
			]
		);
	}

	private function get_labels(): array {
		return [
			'name'               => __( 'Immobilien', 'enteco-immo-connector' ),
			'singular_name'      => __( 'Immobilie', 'enteco-immo-connector' ),
			'add_new'            => __( 'Neue Immobilie', 'enteco-immo-connector' ),
			'add_new_item'       => __( 'Neue Immobilie hinzufügen', 'enteco-immo-connector' ),
			'edit_item'          => __( 'Immobilie bearbeiten', 'enteco-immo-connector' ),
			'new_item'           => __( 'Neue Immobilie', 'enteco-immo-connector' ),
			'view_item'          => __( 'Immobilie ansehen', 'enteco-immo-connector' ),
			'search_items'       => __( 'Immobilien suchen', 'enteco-immo-connector' ),
			'not_found'          => __( 'Keine Immobilien gefunden', 'enteco-immo-connector' ),
			'not_found_in_trash' => __( 'Keine Immobilien im Papierkorb', 'enteco-immo-connector' ),
		];
	}
}
