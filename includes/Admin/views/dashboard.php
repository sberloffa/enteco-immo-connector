<?php
/**
 * Dashboard overview view.
 */

declare( strict_types=1 );

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$provider     = (string) get_option( 'eic_provider', '' );
$field_engine = (string) get_option( 'eic_field_engine', 'native' );

$counts       = wp_count_posts( 'eic_property' );
$prop_count   = (int) ( $counts->publish ?? 0 ) + (int) ( $counts->draft ?? 0 );
$limit        = (int) apply_filters( 'eic_object_limit', 50 );
?>
<div class="wrap eic-wrap">
	<h1><?php esc_html_e( 'Immo Connector — Übersicht', 'enteco-immo-connector' ); ?></h1>

	<div class="eic-dashboard-cards">

		<div class="eic-card">
			<h3><?php esc_html_e( 'Provider', 'enteco-immo-connector' ); ?></h3>
			<p>
				<?php if ( $provider ) : ?>
					<strong><?php echo esc_html( ucfirst( $provider ) ); ?></strong>
				<?php else : ?>
					<span class="eic-warn">
						<?php esc_html_e( 'Kein Provider konfiguriert.', 'enteco-immo-connector' ); ?>
					</span>
					<a href="<?php echo esc_url( admin_url( 'admin.php?page=eic_settings' ) ); ?>">
						<?php esc_html_e( '→ Einstellungen', 'enteco-immo-connector' ); ?>
					</a>
				<?php endif; ?>
			</p>
		</div>

		<div class="eic-card">
			<h3><?php esc_html_e( 'Immobilien', 'enteco-immo-connector' ); ?></h3>
			<p>
				<strong><?php echo esc_html( $prop_count ); ?> / <?php echo esc_html( $limit ); ?></strong>
				<?php esc_html_e( 'Objekte', 'enteco-immo-connector' ); ?>
			</p>
			<div class="eic-progress">
				<div class="eic-progress-bar" style="width: <?php echo esc_attr( min( 100, round( $prop_count / $limit * 100 ) ) ); ?>%"></div>
			</div>
			<?php if ( $prop_count >= $limit ) : ?>
				<p class="eic-warn">
					<?php
					echo wp_kses(
						__( 'Limit erreicht. <a href="https://enteco.de/immo-connector" target="_blank" rel="noopener">PRO für unlimitierte Objekte</a>.', 'enteco-immo-connector' ),
						[ 'a' => [ 'href' => [], 'target' => [], 'rel' => [] ] ]
					);
					?>
				</p>
			<?php endif; ?>
		</div>

		<div class="eic-card">
			<h3><?php esc_html_e( 'Field Engine', 'enteco-immo-connector' ); ?></h3>
			<p><strong><?php echo esc_html( ucfirst( $field_engine ) ); ?></strong></p>
		</div>

	</div>

	<p>
		<a href="<?php echo esc_url( admin_url( 'admin.php?page=eic_import' ) ); ?>" class="button button-primary">
			<?php esc_html_e( '→ Manuellen Import starten', 'enteco-immo-connector' ); ?>
		</a>
	</p>
</div>
