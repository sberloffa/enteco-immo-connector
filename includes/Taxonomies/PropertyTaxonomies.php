<?php
/**
 * Registers the core taxonomies for eic_property (FREE minimal set).
 */

declare( strict_types=1 );

namespace Enteco\ImmoConnector\Taxonomies;

use Enteco\ImmoConnector\PostTypes\PropertyPostType;

class PropertyTaxonomies {

	public function register(): void {
		$this->register_objektart();
		$this->register_vermarktungsart();
		$this->register_nutzungsart();
		$this->register_zustand();
	}

	private function register_objektart(): void {
		register_taxonomy(
			'eic_objektart',
			PropertyPostType::POST_TYPE,
			[
				'labels'            => [
					'name'          => __( 'Objektarten', 'enteco-immo-connector' ),
					'singular_name' => __( 'Objektart', 'enteco-immo-connector' ),
					'search_items'  => __( 'Objektarten suchen', 'enteco-immo-connector' ),
					'all_items'     => __( 'Alle Objektarten', 'enteco-immo-connector' ),
					'edit_item'     => __( 'Objektart bearbeiten', 'enteco-immo-connector' ),
					'add_new_item'  => __( 'Neue Objektart', 'enteco-immo-connector' ),
				],
				'hierarchical'      => true,
				'show_ui'           => true,
				'show_in_rest'      => true,
				'show_admin_column' => true,
				'rewrite'           => [ 'slug' => 'objektart' ],
			]
		);
	}

	private function register_vermarktungsart(): void {
		register_taxonomy(
			'eic_vermarktungsart',
			PropertyPostType::POST_TYPE,
			[
				'labels'            => [
					'name'          => __( 'Vermarktungsarten', 'enteco-immo-connector' ),
					'singular_name' => __( 'Vermarktungsart', 'enteco-immo-connector' ),
					'add_new_item'  => __( 'Neue Vermarktungsart', 'enteco-immo-connector' ),
				],
				'hierarchical'      => false,
				'show_ui'           => true,
				'show_in_rest'      => true,
				'show_admin_column' => true,
				'rewrite'           => [ 'slug' => 'vermarktungsart' ],
			]
		);
	}

	private function register_nutzungsart(): void {
		register_taxonomy(
			'eic_nutzungsart',
			PropertyPostType::POST_TYPE,
			[
				'labels'            => [
					'name'          => __( 'Nutzungsarten', 'enteco-immo-connector' ),
					'singular_name' => __( 'Nutzungsart', 'enteco-immo-connector' ),
					'add_new_item'  => __( 'Neue Nutzungsart', 'enteco-immo-connector' ),
				],
				'hierarchical'      => false,
				'show_ui'           => true,
				'show_in_rest'      => true,
				'rewrite'           => [ 'slug' => 'nutzungsart' ],
			]
		);
	}

	private function register_zustand(): void {
		register_taxonomy(
			'eic_zustand',
			PropertyPostType::POST_TYPE,
			[
				'labels'            => [
					'name'          => __( 'Zustände', 'enteco-immo-connector' ),
					'singular_name' => __( 'Zustand', 'enteco-immo-connector' ),
					'add_new_item'  => __( 'Neuen Zustand', 'enteco-immo-connector' ),
				],
				'hierarchical'      => false,
				'show_ui'           => true,
				'show_in_rest'      => true,
				'rewrite'           => [ 'slug' => 'zustand' ],
			]
		);
	}
}
