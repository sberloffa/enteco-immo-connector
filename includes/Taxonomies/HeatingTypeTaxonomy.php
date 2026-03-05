<?php
/**
 * Taxonomy: eic_heizungsart
 *
 * @package Enteco\ImmoConnector\Taxonomies
 */

declare(strict_types=1);

namespace Enteco\ImmoConnector\Taxonomies;

/** Flat taxonomy for heating type. */
final class HeatingTypeTaxonomy {

	public function register(): void {
		add_action( 'init', [ $this, 'register_taxonomy' ] );
	}

	public function register_taxonomy(): void {
		register_taxonomy(
			'eic_heizungsart',
			[ 'eic_property' ],
			[
				'labels'       => [
					'name'          => _x( 'Heizungsarten', 'taxonomy general name', 'enteco-immo-connector' ),
					'singular_name' => _x( 'Heizungsart', 'taxonomy singular name', 'enteco-immo-connector' ),
					'all_items'     => __( 'Alle Heizungsarten', 'enteco-immo-connector' ),
					'not_found'     => __( 'Keine Heizungsart gefunden.', 'enteco-immo-connector' ),
				],
				'hierarchical' => false,
				'public'       => true,
				'show_ui'      => true,
				'show_in_rest' => true,
				'rewrite'      => [ 'slug' => 'heizungsart' ],
			]
		);
	}
}
