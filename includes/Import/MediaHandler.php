<?php
/**
 * Media Handler – downloads and attaches images.
 *
 * @package Enteco\ImmoConnector\Import
 */

declare(strict_types=1);

namespace Enteco\ImmoConnector\Import;

/**
 * Handles cover image download and gallery URL storage.
 */
final class MediaHandler {

	/**
	 * Download a remote image and attach it to a WP post as the featured image.
	 *
	 * @param int    $post_id   WP post ID.
	 * @param string $image_url Remote image URL.
	 * @return int|null Attachment ID on success, null on failure.
	 */
	public function set_cover_image( int $post_id, string $image_url ): ?int {
		if ( ! $image_url ) {
			return null;
		}

		// Check if this URL was already downloaded (by URL fingerprint).
		$fingerprint  = 'eic_img_' . md5( $image_url );
		$existing_id  = (int) get_transient( $fingerprint );

		if ( $existing_id > 0 && get_post( $existing_id ) ) {
			set_post_thumbnail( $post_id, $existing_id );
			return $existing_id;
		}

		// Ensure WordPress media functions are available.
		if ( ! function_exists( 'media_sideload_image' ) ) {
			require_once ABSPATH . 'wp-admin/includes/media.php';
			require_once ABSPATH . 'wp-admin/includes/file.php';
			require_once ABSPATH . 'wp-admin/includes/image.php';
		}

		$attachment_id = media_sideload_image( $image_url, $post_id, null, 'id' );

		if ( is_wp_error( $attachment_id ) ) {
			return null;
		}

		set_post_thumbnail( $post_id, $attachment_id );
		set_transient( $fingerprint, $attachment_id, WEEK_IN_SECONDS );

		return $attachment_id;
	}

	/**
	 * Store gallery image URLs as serialized postmeta (no download).
	 *
	 * @param int      $post_id WP post ID.
	 * @param string[] $urls    Remote image URLs.
	 */
	public function set_gallery_urls( int $post_id, array $urls ): void {
		if ( empty( $urls ) ) {
			delete_post_meta( $post_id, 'eic_gallery_urls' );
			return;
		}

		$safe_urls = array_filter( array_map( 'esc_url_raw', $urls ) );
		update_post_meta( $post_id, 'eic_gallery_urls', $safe_urls );
	}

	/**
	 * Get stored gallery URLs for a post.
	 *
	 * @param int $post_id WP post ID.
	 * @return string[]
	 */
	public function get_gallery_urls( int $post_id ): array {
		$urls = get_post_meta( $post_id, 'eic_gallery_urls', true );
		return is_array( $urls ) ? $urls : [];
	}
}
