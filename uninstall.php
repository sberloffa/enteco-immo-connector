<?php
/**
 * Uninstall handler – only deletes data when opt-in is set.
 */

declare( strict_types=1 );

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

if ( get_option( 'eic_delete_data_on_uninstall' ) !== 'yes' ) {
	return;
}

global $wpdb;

// Remove all options with eic_ prefix.
$wpdb->query( "DELETE FROM {$wpdb->options} WHERE option_name LIKE 'eic_%'" ); // phpcs:ignore WordPress.DB.DirectDatabaseQuery

// Remove transients.
$wpdb->query( "DELETE FROM {$wpdb->options} WHERE option_name LIKE '_transient_eic_%' OR option_name LIKE '_transient_timeout_eic_%'" ); // phpcs:ignore WordPress.DB.DirectDatabaseQuery

// Remove CPT posts and their postmeta.
$cpt_types = [ 'eic_property', 'eic_agent' ];
foreach ( $cpt_types as $post_type ) {
	$post_ids = $wpdb->get_col( // phpcs:ignore WordPress.DB.DirectDatabaseQuery
		$wpdb->prepare(
			"SELECT ID FROM {$wpdb->posts} WHERE post_type = %s",
			$post_type
		)
	);

	foreach ( $post_ids as $post_id ) {
		$post_id = (int) $post_id;
		$wpdb->query( $wpdb->prepare( "DELETE FROM {$wpdb->postmeta} WHERE post_id = %d AND meta_key LIKE 'eic_%'", $post_id ) ); // phpcs:ignore WordPress.DB.DirectDatabaseQuery
		wp_delete_post( $post_id, true );
	}
}

// Remove taxonomy terms.
$taxonomies = [ 'eic_objektart', 'eic_vermarktungsart', 'eic_nutzungsart', 'eic_zustand' ];
foreach ( $taxonomies as $taxonomy ) {
	$terms = get_terms( [ 'taxonomy' => $taxonomy, 'hide_empty' => false ] );
	if ( is_array( $terms ) ) {
		foreach ( $terms as $term ) {
			wp_delete_term( $term->term_id, $taxonomy );
		}
	}
}
