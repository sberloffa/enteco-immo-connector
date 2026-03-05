<?php
/**
 * Uninstall script – only runs when the plugin is deleted from WP admin.
 *
 * @package Enteco\ImmoConnector
 */

// Block direct access.
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

// Only clean up when the user has explicitly opted in.
if ( 'yes' !== get_option( 'eic_delete_data_on_uninstall' ) ) {
	return;
}

global $wpdb;

// 1. Remove all options with eic_ prefix.
$wpdb->query( "DELETE FROM {$wpdb->options} WHERE option_name LIKE 'eic_%'" );

// 2. Remove all transients with eic_ prefix.
$wpdb->query( "DELETE FROM {$wpdb->options} WHERE option_name LIKE '_transient_eic_%' OR option_name LIKE '_transient_timeout_eic_%'" );

// 3. Unschedule Cron events.
$hooks = [ 'eic_scheduled_import' ];
foreach ( $hooks as $hook ) {
	$ts = wp_next_scheduled( $hook );
	if ( $ts ) {
		wp_unschedule_event( $ts, $hook );
	}
}

// 4. Delete all eic_property and eic_agent posts + their meta.
$post_types = [ 'eic_property', 'eic_agent' ];
foreach ( $post_types as $post_type ) {
	$posts = get_posts( [
		'post_type'      => $post_type,
		'post_status'    => 'any',
		'posts_per_page' => -1,
		'fields'         => 'ids',
	] );

	foreach ( $posts as $post_id ) {
		wp_delete_post( (int) $post_id, true );
	}
}

// 5. Remove taxonomy terms.
$taxonomies = [
	'eic_objektart', 'eic_vermarktungsart', 'eic_nutzungsart',
	'eic_zustand', 'eic_heizungsart', 'eic_ort', 'eic_merkmal',
];
foreach ( $taxonomies as $taxonomy ) {
	$terms = get_terms( [ 'taxonomy' => $taxonomy, 'hide_empty' => false, 'fields' => 'ids' ] );
	if ( ! is_wp_error( $terms ) ) {
		foreach ( $terms as $term_id ) {
			wp_delete_term( (int) $term_id, $taxonomy );
		}
	}
}

// 6. Flush rewrite rules.
flush_rewrite_rules();
