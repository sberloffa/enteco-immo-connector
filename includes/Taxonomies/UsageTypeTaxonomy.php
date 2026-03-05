<?php
/**
 * Taxonomy: eic_nutzungsart (Nutzungsart)
 *
 * @package Enteco\ImmoConnector\Taxonomies
 */

declare(strict_types=1);

namespace Enteco\ImmoConnector\Taxonomies;

/**
 * Flat taxonomy for usage type (Wohnen, Gewerbe, Anlage).
 */
final class UsageTypeTaxonomy {

	public function register(): void {
		add_action( 'init', [ $this, 'register_taxonomy' ] );
	}

	public function register_taxonomy(): void {
		register_taxonomy(
			'eic_nutzungsart',
			[ 'eic_property' ],
			[
				'labels'       => [
					'name'          => _x( 'Nutzungsarten', 'taxonomy general name', 'enteco-immo-connector' ),
					'singular_name' => _x( 'Nutzungsart', 'taxonomy singular name', 'enteco-immo-connector' ),
					'all_items'     => __( 'Alle Nutzungsarten', 'enteco-immo-connector' ),
					'not_found'     => __( 'Keine Nutzungsart gefunden.', 'enteco-immo-connector' ),
				],
				'hierarchical'      => false,
				'public'            => true,
				'show_ui'           => true,
				'show_in_rest'      => true,
				'show_admin_column' => true,
				'rewrite'           => [ 'slug' => 'nutzungsart' ],
			]
		);
	}
}
