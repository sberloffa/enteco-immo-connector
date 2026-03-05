<?php
/**
 * Custom Post Type: eic_property
 *
 * @package Enteco\ImmoConnector\PostTypes
 */

declare(strict_types=1);

namespace Enteco\ImmoConnector\PostTypes;

use Enteco\ImmoConnector\PostTypes\FieldEngine\NativeFieldEngine;

/**
 * Registers the property CPT and its meta fields.
 */
final class PropertyPostType {

	/** Register CPT hooks. */
	public function register(): void {
		add_action( 'init', [ $this, 'register_post_type' ] );
		add_action( 'init', [ $this, 'maybe_flush_rewrites' ], 99 );

		// Register meta fields via the active engine.
		$engine = $this->get_field_engine();
		$engine->register_fields();
	}

	/** Register the eic_property CPT. */
	public function register_post_type(): void {
		$labels = [
			'name'                  => _x( 'Immobilien', 'Post type general name', 'enteco-immo-connector' ),
			'singular_name'         => _x( 'Immobilie', 'Post type singular name', 'enteco-immo-connector' ),
			'menu_name'             => _x( 'Immobilien', 'Admin Menu text', 'enteco-immo-connector' ),
			'name_admin_bar'        => _x( 'Immobilie', 'Add New on Toolbar', 'enteco-immo-connector' ),
			'add_new'               => __( 'Neu hinzufügen', 'enteco-immo-connector' ),
			'add_new_item'          => __( 'Neue Immobilie hinzufügen', 'enteco-immo-connector' ),
			'new_item'              => __( 'Neue Immobilie', 'enteco-immo-connector' ),
			'edit_item'             => __( 'Immobilie bearbeiten', 'enteco-immo-connector' ),
			'view_item'             => __( 'Immobilie ansehen', 'enteco-immo-connector' ),
			'all_items'             => __( 'Alle Immobilien', 'enteco-immo-connector' ),
			'search_items'          => __( 'Immobilien suchen', 'enteco-immo-connector' ),
			'not_found'             => __( 'Keine Immobilien gefunden.', 'enteco-immo-connector' ),
			'not_found_in_trash'    => __( 'Keine Immobilien im Papierkorb gefunden.', 'enteco-immo-connector' ),
		];

		$args = [
			'labels'             => $labels,
			'public'             => true,
			'publicly_queryable' => true,
			'show_ui'            => true,
			'show_in_menu'       => true,
			'query_var'          => true,
			'rewrite'            => [ 'slug' => 'immobilien', 'with_front' => false ],
			'capability_type'    => 'post',
			'has_archive'        => true,
			'hierarchical'       => false,
			'menu_position'      => 26,
			'menu_icon'          => 'dashicons-admin-home',
			'supports'           => [ 'title', 'editor', 'thumbnail', 'excerpt', 'custom-fields' ],
			'show_in_rest'       => true,
			'taxonomies'         => [
				'eic_objektart',
				'eic_vermarktungsart',
				'eic_nutzungsart',
				'eic_zustand',
				'eic_heizungsart',
				'eic_ort',
				'eic_merkmal',
			],
		];

		register_post_type( 'eic_property', $args );
	}

	/** Flush rewrite rules once after activation. */
	public function maybe_flush_rewrites(): void {
		if ( get_option( 'eic_flush_rewrite_rules' ) ) {
			flush_rewrite_rules();
			delete_option( 'eic_flush_rewrite_rules' );
		}
	}

	/**
	 * Return the active field engine instance.
	 *
	 * @return \Enteco\ImmoConnector\PostTypes\FieldEngine\FieldEngineInterface
	 */
	public function get_field_engine(): \Enteco\ImmoConnector\PostTypes\FieldEngine\FieldEngineInterface {
		return new NativeFieldEngine();
	}
}
