<?php
/**
 * Taxonomy: eic_merkmal (Merkmale/Features as Tags)
 *
 * @package Enteco\ImmoConnector\Taxonomies
 */

declare(strict_types=1);

namespace Enteco\ImmoConnector\Taxonomies;

/** Flat tag-cloud taxonomy for property features (balkon, aufzug, etc.). */
final class FeatureTaxonomy {

	public function register(): void {
		add_action( 'init', [ $this, 'register_taxonomy' ] );
	}

	public function register_taxonomy(): void {
		register_taxonomy(
			'eic_merkmal',
			[ 'eic_property' ],
			[
				'labels'       => [
					'name'          => _x( 'Merkmale', 'taxonomy general name', 'enteco-immo-connector' ),
					'singular_name' => _x( 'Merkmal', 'taxonomy singular name', 'enteco-immo-connector' ),
					'all_items'     => __( 'Alle Merkmale', 'enteco-immo-connector' ),
					'not_found'     => __( 'Kein Merkmal gefunden.', 'enteco-immo-connector' ),
				],
				'hierarchical'      => false,
				'public'            => true,
				'show_ui'           => true,
				'show_in_rest'      => true,
				'show_admin_column' => false,
				'rewrite'           => [ 'slug' => 'merkmal' ],
			]
		);
	}
}
