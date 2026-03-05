<?php
/**
 * Single Property Template – fallback (override in your theme).
 *
 * @package Enteco\ImmoConnector
 */

get_header();

while ( have_posts() ) :
	the_post();
	$post_id = get_the_ID();
	?>
	<article id="post-<?php the_ID(); ?>" <?php post_class( 'eic-property' ); ?>>
		<header class="eic-property__header">
			<h1 class="eic-property__title"><?php the_title(); ?></h1>
		</header>

		<?php if ( has_post_thumbnail() ) : ?>
			<div class="eic-property__image">
				<?php the_post_thumbnail( 'large', [ 'class' => 'eic-property__cover', 'loading' => 'eager' ] ); ?>
			</div>
		<?php endif; ?>

		<div class="eic-property__meta">
			<?php
			$kaufpreis  = get_post_meta( $post_id, 'eic_kaufpreis', true );
			$kaltmiete  = get_post_meta( $post_id, 'eic_kaltmiete', true );
			$flaeche    = get_post_meta( $post_id, 'eic_wohnflaeche', true );
			$zimmer     = get_post_meta( $post_id, 'eic_anzahl_zimmer', true );
			$plz        = get_post_meta( $post_id, 'eic_plz', true );
			$ort        = get_post_meta( $post_id, 'eic_ort', true );

			if ( $kaufpreis ) :
				printf(
					'<p class="eic-property__price"><strong>%s:</strong> %s €</p>',
					esc_html__( 'Kaufpreis', 'enteco-immo-connector' ),
					esc_html( number_format( (float) $kaufpreis, 0, ',', '.' ) )
				);
			elseif ( $kaltmiete ) :
				printf(
					'<p class="eic-property__price"><strong>%s:</strong> %s €/Monat</p>',
					esc_html__( 'Kaltmiete', 'enteco-immo-connector' ),
					esc_html( number_format( (float) $kaltmiete, 0, ',', '.' ) )
				);
			endif;

			if ( $flaeche ) :
				printf(
					'<p><strong>%s:</strong> %s m²</p>',
					esc_html__( 'Wohnfläche', 'enteco-immo-connector' ),
					esc_html( $flaeche )
				);
			endif;

			if ( $zimmer ) :
				printf(
					'<p><strong>%s:</strong> %s</p>',
					esc_html__( 'Zimmer', 'enteco-immo-connector' ),
					esc_html( $zimmer )
				);
			endif;

			if ( $plz || $ort ) :
				printf(
					'<p><strong>%s:</strong> %s %s</p>',
					esc_html__( 'Adresse', 'enteco-immo-connector' ),
					esc_html( $plz ),
					esc_html( $ort )
				);
			endif;
			?>
		</div>

		<div class="eic-property__description">
			<?php the_content(); ?>
		</div>
	</article>
	<?php
endwhile;

get_footer();
