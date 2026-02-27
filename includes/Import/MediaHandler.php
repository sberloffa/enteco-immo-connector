<?php
/**
 * Downloads a remote image URL and sets it as the post thumbnail (featured image).
 * Uses a SHA1 URL fingerprint to avoid duplicate attachments on re-import.
 */

declare( strict_types=1 );

namespace Enteco\ImmoConnector\Import;

class MediaHandler {

	public function set_thumbnail( int $post_id, string $url, string $title ): bool {
		$url = esc_url_raw( $url );
		if ( empty( $url ) ) {
			return false;
		}

		$existing_id = $this->find_by_source_url( $url );
		if ( $existing_id > 0 ) {
			return (bool) set_post_thumbnail( $post_id, $existing_id );
		}

		$attachment_id = $this->sideload_image( $url, $post_id, $title );
		if ( $attachment_id <= 0 ) {
			return false;
		}

		return (bool) set_post_thumbnail( $post_id, $attachment_id );
	}

	private function find_by_source_url( string $url ): int {
		$fingerprint = sha1( $url );

		$query = new \WP_Query( [
			'post_type'      => 'attachment',
			'post_status'    => 'inherit',
			'posts_per_page' => 1,
			'fields'         => 'ids',
			'meta_query'     => [
				[
					'key'   => '_eic_source_url_hash',
					'value' => $fingerprint,
				],
			],
		] );

		$posts = $query->posts;
		wp_reset_postdata();

		return ! empty( $posts ) ? (int) $posts[0] : 0;
	}

	private function sideload_image( string $url, int $post_id, string $title ): int {
		if ( ! function_exists( 'media_sideload_image' ) ) {
			require_once ABSPATH . 'wp-admin/includes/media.php';
			require_once ABSPATH . 'wp-admin/includes/file.php';
			require_once ABSPATH . 'wp-admin/includes/image.php';
		}

		$attachment_id = media_sideload_image(
			$url,
			$post_id,
			sanitize_text_field( $title ),
			'id'
		);

		if ( is_wp_error( $attachment_id ) ) {
			return 0;
		}

		$attachment_id = (int) $attachment_id;
		update_post_meta( $attachment_id, '_eic_source_url_hash', sha1( $url ) );

		return $attachment_id;
	}
}
