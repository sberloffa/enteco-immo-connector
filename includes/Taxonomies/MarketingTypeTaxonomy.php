<?php
/**
 * Taxonomy: eic_vermarktungsart (Vermarktungsart)
 *
 * @package Enteco\ImmoConnector\Taxonomies
 */

declare(strict_types=1);

namespace Enteco\ImmoConnector\Taxonomies;

/**
 * Flat taxonomy for marketing type (Kauf, Miete, Erbpacht, Leasing).
 */
final class MarketingTypeTaxonomy {

	public function register(): void {
		add_action( 'init', [ $this, 'register_taxonomy' ] );
	}

	public function register_taxonomy(): void {
		$labels = [
			'name'          => _x( 'Vermarktungsarten', 'taxonomy general name', 'enteco-immo-connector' ),
			'singular_name' => _x( 'Vermarktungsart', 'taxonomy singular name', 'enteco-immo-connector' ),
			'all_items'     => __( 'Alle Vermarktungsarten', 'enteco-immo-connector' ),
			'edit_item'     => __( 'Vermarktungsart bearbeiten', 'enteco-immo-connector' ),
			'add_new_item'  => __( 'Neue Vermarktungsart', 'enteco-immo-connector' ),
			'not_found'     => __( 'Keine Vermarktungsart gefunden.', 'enteco-immo-connector' ),
		];

		register_taxonomy(
			'eic_vermarktungsart',
			[ 'eic_property' ],
			[
				'labels'            => $labels,
				'hierarchical'      => false,
				'public'            => true,
				'show_ui'           => true,
				'show_in_rest'      => true,
				'show_admin_column' => true,
				'rewrite'           => [ 'slug' => 'vermarktungsart' ],
			]
		);
	}
}
