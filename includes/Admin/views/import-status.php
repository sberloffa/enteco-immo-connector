<?php
/**
 * Import status page view — manual trigger button and last-run status.
 *
 * @var string    $provider       Active provider slug.
 * @var string    $import_mode    'automatic'|'manual'.
 * @var int|false $next_scheduled Unix timestamp of next scheduled cron run, or false.
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
			<table class="form-table" style="margin-bottom:0">
				<tr>
					<th scope="row" style="width:160px"><?php esc_html_e( 'Provider', 'enteco-immo-connector' ); ?></th>
					<td><strong><?php echo esc_html( ucfirst( $provider ) ); ?></strong></td>
				</tr>
				<tr>
					<th scope="row"><?php esc_html_e( 'Import-Modus', 'enteco-immo-connector' ); ?></th>
					<td>
						<?php if ( $import_mode === 'automatic' ) : ?>
							<strong><?php esc_html_e( 'Automatisch (täglich)', 'enteco-immo-connector' ); ?></strong>
							<?php if ( $next_scheduled ) : ?>
								&mdash;
								<?php
								printf(
									/* translators: %s: formatted date/time */
									esc_html__( 'Nächster Lauf: %s', 'enteco-immo-connector' ),
									'<strong>' . esc_html( wp_date( get_option( 'date_format' ) . ' ' . get_option( 'time_format' ), $next_scheduled ) ) . '</strong>'
								);
								?>
							<?php else : ?>
								<span class="eic-warn"><?php esc_html_e( '(kein Cron geplant — Plugin neu aktivieren)', 'enteco-immo-connector' ); ?></span>
							<?php endif; ?>
						<?php else : ?>
							<strong><?php esc_html_e( 'Nur manuell', 'enteco-immo-connector' ); ?></strong>
						<?php endif; ?>
						<p class="description">
							<?php
							echo wp_kses(
								__( 'Modus ändern unter <a href="' . esc_url( admin_url( 'admin.php?page=eic_settings' ) ) . '">Einstellungen</a>.', 'enteco-immo-connector' ),
								[ 'a' => [ 'href' => [] ] ]
							);
							?>
						</p>
					</td>
				</tr>
			</table>

			<hr style="margin:16px 0">

			<p><?php esc_html_e( 'Der Import lädt alle Objekte vom konfigurierten Provider und aktualisiert oder erstellt die entsprechenden WordPress-Beiträge.', 'enteco-immo-connector' ); ?></p>

			<button id="eic-start-import" class="button button-primary button-large">
				<?php esc_html_e( 'Import jetzt starten', 'enteco-immo-connector' ); ?>
			</button>

			<span id="eic-import-spinner" class="spinner" style="float:none;margin-top:4px;display:none;"></span>
		</div>

		<div id="eic-import-result" style="display:none; margin-top:16px;"></div>

	<?php endif; ?>
</div>
