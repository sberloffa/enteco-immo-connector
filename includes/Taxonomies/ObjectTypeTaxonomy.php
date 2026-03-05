<?php
/**
 * Taxonomy: eic_objektart (Objektart)
 *
 * @package Enteco\ImmoConnector\Taxonomies
 */

declare(strict_types=1);

namespace Enteco\ImmoConnector\Taxonomies;

/**
 * Hierarchical taxonomy for property object types.
 */
final class ObjectTypeTaxonomy {

	/** Register taxonomy hooks. */
	public function register(): void {
		add_action( 'init', [ $this, 'register_taxonomy' ] );
	}

	/** Register the eic_objektart taxonomy. */
	public function register_taxonomy(): void {
		$labels = [
			'name'          => _x( 'Objektarten', 'taxonomy general name', 'enteco-immo-connector' ),
			'singular_name' => _x( 'Objektart', 'taxonomy singular name', 'enteco-immo-connector' ),
			'all_items'     => __( 'Alle Objektarten', 'enteco-immo-connector' ),
			'edit_item'     => __( 'Objektart bearbeiten', 'enteco-immo-connector' ),
			'add_new_item'  => __( 'Neue Objektart', 'enteco-immo-connector' ),
			'not_found'     => __( 'Keine Objektart gefunden.', 'enteco-immo-connector' ),
		];

		register_taxonomy(
			'eic_objektart',
			[ 'eic_property' ],
			[
				'labels'            => $labels,
				'hierarchical'      => true,
				'public'            => true,
				'show_ui'           => true,
				'show_in_menu'      => true,
				'show_in_rest'      => true,
				'show_admin_column' => true,
				'rewrite'           => [ 'slug' => 'objektart' ],
			]
		);
	}
}
