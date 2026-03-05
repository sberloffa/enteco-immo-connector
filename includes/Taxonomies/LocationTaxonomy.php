<?php
/**
 * Taxonomy: eic_ort (Ort/Standort)
 *
 * @package Enteco\ImmoConnector\Taxonomies
 */

declare(strict_types=1);

namespace Enteco\ImmoConnector\Taxonomies;

/** Hierarchical taxonomy for location (Bundesland > PLZ/Ort). */
final class LocationTaxonomy {

	public function register(): void {
		add_action( 'init', [ $this, 'register_taxonomy' ] );
	}

	public function register_taxonomy(): void {
		register_taxonomy(
			'eic_ort',
			[ 'eic_property' ],
			[
				'labels'       => [
					'name'          => _x( 'Orte', 'taxonomy general name', 'enteco-immo-connector' ),
					'singular_name' => _x( 'Ort', 'taxonomy singular name', 'enteco-immo-connector' ),
					'all_items'     => __( 'Alle Orte', 'enteco-immo-connector' ),
					'not_found'     => __( 'Kein Ort gefunden.', 'enteco-immo-connector' ),
				],
				'hierarchical'      => true,
				'public'            => true,
				'show_ui'           => true,
				'show_in_rest'      => true,
				'show_admin_column' => false,
				'rewrite'           => [ 'slug' => 'ort' ],
			]
		);
	}
}
