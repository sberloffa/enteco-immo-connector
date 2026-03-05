<?php
/**
 * Archive Property Template – fallback (override in your theme).
 *
 * @package Enteco\ImmoConnector
 */

get_header();
?>
<div class="eic-property-archive">
	<header class="eic-property-archive__header">
		<h1 class="eic-property-archive__title">
			<?php echo esc_html__( 'Immobilien', 'enteco-immo-connector' ); ?>
		</h1>
	</header>

	<?php if ( have_posts() ) : ?>
		<div class="eic-property-grid">
			<?php
			while ( have_posts() ) :
				the_post();
				$post_id   = get_the_ID();
				$kaufpreis = get_post_meta( $post_id, 'eic_kaufpreis', true );
				$kaltmiete = get_post_meta( $post_id, 'eic_kaltmiete', true );
				$flaeche   = get_post_meta( $post_id, 'eic_wohnflaeche', true );
				$zimmer    = get_post_meta( $post_id, 'eic_anzahl_zimmer', true );
				$ort       = get_post_meta( $post_id, 'eic_ort', true );
				?>
				<article class="eic-property-card" id="post-<?php the_ID(); ?>">
					<?php if ( has_post_thumbnail() ) : ?>
						<a href="<?php the_permalink(); ?>" class="eic-property-card__img-wrap">
							<?php the_post_thumbnail( 'medium', [ 'class' => 'eic-property-card__img', 'loading' => 'lazy' ] ); ?>
						</a>
					<?php endif; ?>

					<div class="eic-property-card__body">
						<h2 class="eic-property-card__title">
							<a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
						</h2>

						<?php if ( $ort ) : ?>
							<p class="eic-property-card__location"><?php echo esc_html( $ort ); ?></p>
						<?php endif; ?>

						<div class="eic-property-card__details">
							<?php if ( $flaeche ) : ?>
								<span><?php echo esc_html( $flaeche ); ?> m²</span>
							<?php endif; ?>
							<?php if ( $zimmer ) : ?>
								<span><?php echo esc_html( $zimmer ); ?> <?php echo esc_html__( 'Zimmer', 'enteco-immo-connector' ); ?></span>
							<?php endif; ?>
						</div>

						<?php if ( $kaufpreis ) : ?>
							<p class="eic-property-card__price">
								<?php echo esc_html( number_format( (float) $kaufpreis, 0, ',', '.' ) ); ?> €
							</p>
						<?php elseif ( $kaltmiete ) : ?>
							<p class="eic-property-card__price">
								<?php echo esc_html( number_format( (float) $kaltmiete, 0, ',', '.' ) ); ?> €/Mo.
							</p>
						<?php endif; ?>
					</div>
				</article>
			<?php endwhile; ?>
		</div>

		<?php the_posts_pagination(); ?>

	<?php else : ?>
		<p><?php echo esc_html__( 'Keine Immobilien gefunden.', 'enteco-immo-connector' ); ?></p>
	<?php endif; ?>
</div>
<?php

get_footer();
