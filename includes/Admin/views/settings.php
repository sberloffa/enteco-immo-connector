<?php
/**
 * Settings page view — provider credentials and plugin options.
 *
 * Variables provided by SettingsPage::render():
 *
 * @var string $provider            Active provider slug.
 * @var string $j_username          Justimmo username.
 * @var bool   $j_has_password      Whether a Justimmo password is stored.
 * @var string $oo_token            OnOffice API token.
 * @var bool   $oo_has_secret       Whether an OnOffice secret is stored.
 * @var string $field_engine        Active field engine slug.
 * @var string $delete_on_uninstall 'yes'|'no'.
 */

declare( strict_types=1 );

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<div class="wrap eic-wrap">
	<h1><?php esc_html_e( 'Immo Connector — Einstellungen', 'enteco-immo-connector' ); ?></h1>

	<?php settings_errors( 'eic_settings' ); ?>

	<form method="post">
		<?php wp_nonce_field( 'eic_save_settings', 'eic_settings_nonce' ); ?>

		<h2 class="nav-tab-wrapper">
			<span class="nav-tab nav-tab-active"><?php esc_html_e( 'Provider', 'enteco-immo-connector' ); ?></span>
		</h2>

		<table class="form-table">

			<!-- Provider Selection -->
			<tr>
				<th scope="row"><?php esc_html_e( 'Aktiver Provider', 'enteco-immo-connector' ); ?></th>
				<td>
					<label>
						<input type="radio" name="eic_provider" value="justimmo"
							<?php checked( $provider, 'justimmo' ); ?>>
						Justimmo
					</label>
					&nbsp;&nbsp;
					<label>
						<input type="radio" name="eic_provider" value="onoffice"
							<?php checked( $provider, 'onoffice' ); ?>>
						OnOffice
					</label>
					<p class="description">
						<?php esc_html_e( 'FREE: genau ein Provider aktiv. Mehrere Provider parallel im PRO-Addon.', 'enteco-immo-connector' ); ?>
					</p>
				</td>
			</tr>
		</table>

		<!-- Justimmo Credentials -->
		<div id="eic-justimmo-section" <?php echo $provider !== 'justimmo' ? 'style="display:none"' : ''; ?>>
			<h2><?php esc_html_e( 'Justimmo Zugangsdaten', 'enteco-immo-connector' ); ?></h2>
			<table class="form-table">
				<tr>
					<th scope="row"><label for="eic_justimmo_username"><?php esc_html_e( 'Benutzername', 'enteco-immo-connector' ); ?></label></th>
					<td>
						<input type="text" id="eic_justimmo_username" name="eic_justimmo_username"
							value="<?php echo esc_attr( $j_username ); ?>"
							class="regular-text" autocomplete="off">
					</td>
				</tr>
				<tr>
					<th scope="row"><label for="eic_justimmo_password"><?php esc_html_e( 'Passwort', 'enteco-immo-connector' ); ?></label></th>
					<td>
						<input type="password" id="eic_justimmo_password" name="eic_justimmo_password"
							value="" placeholder="<?php echo $j_has_password ? esc_attr__( '(gesetzt — leer lassen zum Beibehalten)', 'enteco-immo-connector' ) : ''; ?>"
							class="regular-text" autocomplete="new-password">
					</td>
				</tr>
			</table>
		</div>

		<!-- OnOffice Credentials -->
		<div id="eic-onoffice-section" <?php echo $provider !== 'onoffice' ? 'style="display:none"' : ''; ?>>
			<h2><?php esc_html_e( 'OnOffice Zugangsdaten', 'enteco-immo-connector' ); ?></h2>
			<table class="form-table">
				<tr>
					<th scope="row"><label for="eic_onoffice_token"><?php esc_html_e( 'API Token', 'enteco-immo-connector' ); ?></label></th>
					<td>
						<input type="text" id="eic_onoffice_token" name="eic_onoffice_token"
							value="<?php echo esc_attr( $oo_token ); ?>"
							class="regular-text" autocomplete="off">
					</td>
				</tr>
				<tr>
					<th scope="row"><label for="eic_onoffice_secret"><?php esc_html_e( 'API Secret', 'enteco-immo-connector' ); ?></label></th>
					<td>
						<input type="password" id="eic_onoffice_secret" name="eic_onoffice_secret"
							value="" placeholder="<?php echo $oo_has_secret ? esc_attr__( '(gesetzt — leer lassen zum Beibehalten)', 'enteco-immo-connector' ) : ''; ?>"
							class="regular-text" autocomplete="new-password">
						<p class="description">
							<?php esc_html_e( 'Hinweis: OnOffice verwendet HMAC-SHA256. Stelle sicher, dass die Serverzeit synchron ist.', 'enteco-immo-connector' ); ?>
						</p>
					</td>
				</tr>
			</table>
		</div>

		<h2><?php esc_html_e( 'Allgemein', 'enteco-immo-connector' ); ?></h2>
		<table class="form-table">
			<tr>
				<th scope="row"><?php esc_html_e( 'Field Engine', 'enteco-immo-connector' ); ?></th>
				<td>
					<strong><?php echo esc_html( ucfirst( $field_engine ) ); ?></strong>
					<p class="description">
						<?php esc_html_e( 'Die Field Engine kann nach dem Onboarding nicht mehr geändert werden.', 'enteco-immo-connector' ); ?>
					</p>
				</td>
			</tr>
			<tr>
				<th scope="row"><?php esc_html_e( 'Objektlimit (FREE)', 'enteco-immo-connector' ); ?></th>
				<td>
					<strong>50</strong>
					<p class="description">
						<?php
						echo wp_kses(
							__( 'Max. 50 Objekte im FREE-Plan. <a href="https://enteco.de/immo-connector" target="_blank" rel="noopener">Upgrade auf PRO</a> für unlimitierte Objekte.', 'enteco-immo-connector' ),
							[ 'a' => [ 'href' => [], 'target' => [], 'rel' => [] ] ]
						);
						?>
					</p>
				</td>
			</tr>
			<tr>
				<th scope="row"><?php esc_html_e( 'Daten bei Deinstallation löschen', 'enteco-immo-connector' ); ?></th>
				<td>
					<label>
						<input type="checkbox" name="eic_delete_data_on_uninstall" value="yes"
							<?php checked( $delete_on_uninstall, 'yes' ); ?>>
						<?php esc_html_e( 'Alle Plugin-Daten (Posts, Optionen, Meta) beim Deinstallieren entfernen.', 'enteco-immo-connector' ); ?>
					</label>
				</td>
			</tr>
		</table>

		<?php submit_button( __( 'Einstellungen speichern', 'enteco-immo-connector' ) ); ?>
	</form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
	var radios = document.querySelectorAll('input[name="eic_provider"]');
	function toggleSections() {
		var val = document.querySelector('input[name="eic_provider"]:checked');
		val = val ? val.value : '';
		document.getElementById('eic-justimmo-section').style.display = val === 'justimmo' ? '' : 'none';
		document.getElementById('eic-onoffice-section').style.display  = val === 'onoffice'  ? '' : 'none';
	}
	radios.forEach(function (r) { r.addEventListener('change', toggleSections); });
	toggleSections();
});
</script>
