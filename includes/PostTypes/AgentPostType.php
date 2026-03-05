<?php
/**
 * Custom Post Type: eic_agent (Makler/Mitarbeiter)
 *
 * @package Enteco\ImmoConnector\PostTypes
 */

declare(strict_types=1);

namespace Enteco\ImmoConnector\PostTypes;

/**
 * Registers the agent CPT.
 */
final class AgentPostType {

	/** Register CPT hooks. */
	public function register(): void {
		add_action( 'init', [ $this, 'register_post_type' ] );
	}

	/** Register the eic_agent CPT. */
	public function register_post_type(): void {
		$labels = [
			'name'               => _x( 'Makler', 'Post type general name', 'enteco-immo-connector' ),
			'singular_name'      => _x( 'Makler', 'Post type singular name', 'enteco-immo-connector' ),
			'menu_name'          => _x( 'Makler', 'Admin Menu text', 'enteco-immo-connector' ),
			'add_new'            => __( 'Neu hinzufügen', 'enteco-immo-connector' ),
			'add_new_item'       => __( 'Neuen Makler hinzufügen', 'enteco-immo-connector' ),
			'edit_item'          => __( 'Makler bearbeiten', 'enteco-immo-connector' ),
			'view_item'          => __( 'Makler ansehen', 'enteco-immo-connector' ),
			'all_items'          => __( 'Alle Makler', 'enteco-immo-connector' ),
			'not_found'          => __( 'Kein Makler gefunden.', 'enteco-immo-connector' ),
			'not_found_in_trash' => __( 'Kein Makler im Papierkorb.', 'enteco-immo-connector' ),
		];

		$args = [
			'labels'          => $labels,
			'public'          => false,
			'show_ui'         => true,
			'show_in_menu'    => 'eic-dashboard',
			'capability_type' => 'post',
			'hierarchical'    => false,
			'menu_icon'       => 'dashicons-admin-users',
			'supports'        => [ 'title', 'thumbnail', 'custom-fields' ],
			'show_in_rest'    => false,
			'rewrite'         => false,
		];

		register_post_type( 'eic_agent', $args );
	}
}
