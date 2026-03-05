<?php
/**
 * Import Status Page – manual trigger + last-run status.
 *
 * @package Enteco\ImmoConnector\Admin
 */

declare(strict_types=1);

namespace Enteco\ImmoConnector\Admin;

use Enteco\ImmoConnector\Import\ImportEngine;

/**
 * Admin page for triggering manual imports and viewing status.
 */
final class ImportStatusPage {

	private const NONCE_ACTION = 'eic_manual_import';

	/** Register hooks. */
	public function register(): void {
		add_action( 'admin_menu', [ $this, 'add_submenu' ] );
		add_action( 'wp_ajax_eic_manual_import', [ $this, 'ajax_run_import' ] );
	}

	/** Add submenu page. */
	public function add_submenu(): void {
		add_submenu_page(
			'eic-dashboard',
			esc_html__( 'Import', 'enteco-immo-connector' ),
			esc_html__( 'Import', 'enteco-immo-connector' ),
			'manage_options',
			'eic-import-status',
			[ $this, 'render' ]
		);
	}

	/** Render the import status page. */
	public function render(): void {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( esc_html__( 'Keine Berechtigung.', 'enteco-immo-connector' ) );
		}

		$last = get_option( 'eic_last_import_status', null );
		?>
		<div class="wrap eic-import-status">
			<h1><?php echo esc_html__( 'Import', 'enteco-immo-connector' ); ?></h1>

			<div id="eic-import-actions">
				<button type="button" id="eic-run-import" class="button button-primary">
					<?php echo esc_html__( 'Import jetzt starten', 'enteco-immo-connector' ); ?>
				</button>
				<span id="eic-import-spinner" class="spinner" style="display:none;visibility:visible;"></span>
				<p id="eic-import-message" class="description"></p>
			</div>

			<?php if ( $last ) : ?>
				<h2><?php echo esc_html__( 'Letzter Import', 'enteco-immo-connector' ); ?></h2>
				<table class="widefat eic-import-table">
					<tr>
						<th><?php echo esc_html__( 'Zeitpunkt', 'enteco-immo-connector' ); ?></th>
						<td><?php echo esc_html( $last['time'] ?? '-' ); ?></td>
					</tr>
					<tr>
						<th><?php echo esc_html__( 'Status', 'enteco-immo-connector' ); ?></th>
						<td><?php echo esc_html( $last['status'] ?? '-' ); ?></td>
					</tr>
					<?php if ( isset( $last['summary'] ) ) : $s = $last['summary']; ?>
					<tr>
						<th><?php echo esc_html__( 'Neu', 'enteco-immo-connector' ); ?></th>
						<td><?php echo esc_html( $s['new'] ?? 0 ); ?></td>
					</tr>
					<tr>
						<th><?php echo esc_html__( 'Aktualisiert', 'enteco-immo-connector' ); ?></th>
						<td><?php echo esc_html( $s['updated'] ?? 0 ); ?></td>
					</tr>
					<tr>
						<th><?php echo esc_html__( 'Fehler', 'enteco-immo-connector' ); ?></th>
						<td><?php echo esc_html( $s['errors'] ?? 0 ); ?></td>
					</tr>
					<?php endif; ?>
				</table>
			<?php endif; ?>

			<script>
			document.getElementById('eic-run-import')?.addEventListener('click', function() {
				var btn     = this;
				var spinner = document.getElementById('eic-import-spinner');
				var msg     = document.getElementById('eic-import-message');

				btn.disabled = true;
				spinner.style.display = 'inline-block';
				msg.textContent = <?php echo wp_json_encode( __( 'Import läuft…', 'enteco-immo-connector' ) ); ?>;

				fetch(ajaxurl, {
					method: 'POST',
					headers: {'Content-Type': 'application/x-www-form-urlencoded'},
					body: 'action=eic_manual_import&nonce=' + <?php echo wp_json_encode( wp_create_nonce( self::NONCE_ACTION ) ); ?>,
				})
				.then(r => r.json())
				.then(data => {
					spinner.style.display = 'none';
					btn.disabled = false;
					msg.textContent = data.success
						? <?php echo wp_json_encode( __( 'Import abgeschlossen.', 'enteco-immo-connector' ) ); ?>
						: (data.data?.message || <?php echo wp_json_encode( __( 'Fehler beim Import.', 'enteco-immo-connector' ) ); ?>);
					if (data.success) {
						setTimeout(() => location.reload(), 2000);
					}
				})
				.catch(() => {
					spinner.style.display = 'none';
					btn.disabled = false;
					msg.textContent = <?php echo wp_json_encode( __( 'Netzwerkfehler.', 'enteco-immo-connector' ) ); ?>;
				});
			});
			</script>
		</div>
		<?php
	}

	/** AJAX handler: run manual import. */
	public function ajax_run_import(): void {
		check_ajax_referer( self::NONCE_ACTION, 'nonce' );

		if ( ! current_user_can( 'manage_options' ) ) {
			wp_send_json_error( [ 'message' => __( 'Keine Berechtigung.', 'enteco-immo-connector' ) ] );
			return;
		}

		try {
			$engine  = new ImportEngine();
			$summary = $engine->run_manual();
			wp_send_json_success( [ 'summary' => $summary ] );
		} catch ( \Throwable $e ) {
			wp_send_json_error( [ 'message' => $e->getMessage() ] );
		}
	}
}
