<?php
/**
 * Onboarding wizard view — first-run field engine selection.
 *
 * @var string $field_engine Currently set engine (empty on first run).
 */

declare( strict_types=1 );

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<div class="wrap eic-wrap">
	<h1><?php esc_html_e( 'Willkommen beim Enteco Immo Connector', 'enteco-immo-connector' ); ?></h1>
	<div class="eic-onboarding-card">
		<h2><?php esc_html_e( 'Schritt 1: Field Engine wählen', 'enteco-immo-connector' ); ?></h2>
		<p>
			<?php esc_html_e( 'Wähle, wie Immobilien-Felder in WordPress gespeichert werden. Diese Einstellung kann nach dem ersten Setup nicht mehr geändert werden.', 'enteco-immo-connector' ); ?>
		</p>

		<form method="post">
			<?php wp_nonce_field( 'eic_onboarding', 'eic_onboarding_nonce' ); ?>

			<table class="form-table">
				<tr>
					<th scope="row"><?php esc_html_e( 'Field Engine', 'enteco-immo-connector' ); ?></th>
					<td>
						<label>
							<input type="radio" name="eic_field_engine" value="native" checked>
							<strong><?php esc_html_e( 'Native (WordPress postmeta)', 'enteco-immo-connector' ); ?></strong>
						</label>
						<p class="description">
							<?php esc_html_e( 'Keine zusätzlichen Plugins erforderlich. Empfohlen für die meisten Installationen.', 'enteco-immo-connector' ); ?>
						</p>
						<p class="description eic-pro-notice">
							<?php
							echo wp_kses(
								__( '<strong>ACF &amp; MetaBox-Unterstützung</strong> sind im <a href="https://enteco.de/immo-connector" target="_blank" rel="noopener">PRO-Addon</a> verfügbar.', 'enteco-immo-connector' ),
								[
									'strong' => [],
									'a'      => [ 'href' => [], 'target' => [], 'rel' => [] ],
								]
							);
							?>
						</p>
					</td>
				</tr>
			</table>

			<?php submit_button( __( 'Setup abschließen', 'enteco-immo-connector' ) ); ?>
		</form>
	</div>
</div>
