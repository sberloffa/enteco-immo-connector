<?php
/**
 * Main Admin Menu Page (Dashboard).
 *
 * @package Enteco\ImmoConnector\Admin
 */

declare(strict_types=1);

namespace Enteco\ImmoConnector\Admin;

/**
 * Registers the top-level admin menu for the plugin.
 */
final class AdminPage {

	/** Register hooks. */
	public function register(): void {
		add_action( 'admin_menu', [ $this, 'add_menu' ] );
	}

	/** Add top-level menu and sub-pages. */
	public function add_menu(): void {
		add_menu_page(
			esc_html__( 'Immo Connector', 'enteco-immo-connector' ),
			esc_html__( 'Immo Connector', 'enteco-immo-connector' ),
			'manage_options',
			'eic-dashboard',
			[ $this, 'render' ],
			'dashicons-admin-home',
			26
		);
	}

	/** Render the dashboard page. */
	public function render(): void {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( esc_html__( 'Sie haben keine Berechtigung für diese Seite.', 'enteco-immo-connector' ) );
		}

		$last_import = get_option( 'eic_last_import_status', null );
		?>
		<div class="wrap eic-dashboard">
			<h1><?php echo esc_html__( 'Enteco Immo Connector', 'enteco-immo-connector' ); ?></h1>

			<div class="eic-dashboard__cards">
				<div class="eic-card">
					<h2><?php echo esc_html__( 'Immobilien', 'enteco-immo-connector' ); ?></h2>
					<p class="eic-card__count">
						<?php echo esc_html( wp_count_posts( 'eic_property' )->publish ?? 0 ); ?>
					</p>
					<a href="<?php echo esc_url( admin_url( 'edit.php?post_type=eic_property' ) ); ?>" class="button">
						<?php echo esc_html__( 'Alle anzeigen', 'enteco-immo-connector' ); ?>
					</a>
				</div>

				<div class="eic-card">
					<h2><?php echo esc_html__( 'Letzter Import', 'enteco-immo-connector' ); ?></h2>
					<?php if ( $last_import ) : ?>
						<p><?php echo esc_html( $last_import['time'] ?? '' ); ?></p>
						<p class="eic-status eic-status--<?php echo esc_attr( $last_import['status'] ?? 'idle' ); ?>">
							<?php echo esc_html( $last_import['status'] ?? '' ); ?>
						</p>
					<?php else : ?>
						<p><?php echo esc_html__( 'Noch kein Import ausgeführt.', 'enteco-immo-connector' ); ?></p>
					<?php endif; ?>
					<a href="<?php echo esc_url( admin_url( 'admin.php?page=eic-import-status' ) ); ?>" class="button button-primary">
						<?php echo esc_html__( 'Import starten', 'enteco-immo-connector' ); ?>
					</a>
				</div>
			</div>

			<div class="eic-upsell notice notice-info inline">
				<p>
					<strong><?php echo esc_html__( 'PRO-Version verfügbar', 'enteco-immo-connector' ); ?></strong> –
					<?php echo esc_html__( 'Automatischer Import, mehr Felder, PageBuilder-Integration und mehr.', 'enteco-immo-connector' ); ?>
				</p>
			</div>
		</div>
		<?php
	}
}
