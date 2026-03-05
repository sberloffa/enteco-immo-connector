<?php
/**
 * Taxonomy: eic_zustand (Zustand)
 *
 * @package Enteco\ImmoConnector\Taxonomies
 */

declare(strict_types=1);

namespace Enteco\ImmoConnector\Taxonomies;

/**
 * Flat taxonomy for property condition.
 */
final class ConditionTaxonomy {

	public function register(): void {
		add_action( 'init', [ $this, 'register_taxonomy' ] );
	}

	public function register_taxonomy(): void {
		register_taxonomy(
			'eic_zustand',
			[ 'eic_property' ],
			[
				'labels'       => [
					'name'          => _x( 'Zustände', 'taxonomy general name', 'enteco-immo-connector' ),
					'singular_name' => _x( 'Zustand', 'taxonomy singular name', 'enteco-immo-connector' ),
					'all_items'     => __( 'Alle Zustände', 'enteco-immo-connector' ),
					'not_found'     => __( 'Kein Zustand gefunden.', 'enteco-immo-connector' ),
				],
				'hierarchical' => false,
				'public'       => true,
				'show_ui'      => true,
				'show_in_rest' => true,
				'rewrite'      => [ 'slug' => 'zustand' ],
			]
		);
	}
}
