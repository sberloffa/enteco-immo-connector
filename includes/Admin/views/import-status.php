<?php
/**
 * Import status page view — manual trigger button and last-run status.
 *
 * @var string $provider Active provider slug.
 */

declare( strict_types=1 );

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<div class="wrap eic-wrap">
	<h1><?php esc_html_e( 'Immo Connector — Import', 'enteco-immo-connector' ); ?></h1>

	<?php if ( empty( $provider ) ) : ?>
		<div class="notice notice-warning">
			<p>
				<?php
				echo wp_kses(
					__( 'Kein Provider konfiguriert. Bitte zuerst die <a href="' . esc_url( admin_url( 'admin.php?page=eic_settings' ) ) . '">Einstellungen</a> ausfüllen.', 'enteco-immo-connector' ),
					[ 'a' => [ 'href' => [] ] ]
				);
				?>
			</p>
		</div>
	<?php else : ?>

		<div class="eic-import-box">
			<p>
				<?php
				printf(
					/* translators: %s: provider name */
					esc_html__( 'Provider: %s', 'enteco-immo-connector' ),
					'<strong>' . esc_html( ucfirst( $provider ) ) . '</strong>'
				);
				?>
			</p>
			<p><?php esc_html_e( 'Der Import lädt alle Objekte vom konfigurierten Provider und aktualisiert oder erstellt die entsprechenden WordPress-Beiträge.', 'enteco-immo-connector' ); ?></p>

			<button id="eic-start-import" class="button button-primary button-large">
				<?php esc_html_e( 'Import jetzt starten', 'enteco-immo-connector' ); ?>
			</button>

			<span id="eic-import-spinner" class="spinner" style="float:none;margin-top:4px;display:none;"></span>
		</div>

		<div id="eic-import-result" style="display:none; margin-top:16px;"></div>

	<?php endif; ?>
</div>
