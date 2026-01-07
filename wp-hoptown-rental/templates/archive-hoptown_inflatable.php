<?php
/**
 * Archive template for Inflatables
 *
 * @package Hoptown_Rental
 */

get_header();

?>
<main id="primary" class="site-main hoptown-inflatables-archive">
	<header class="page-header">
		<h1 class="page-title"><?php echo esc_html( post_type_archive_title( "", false ) ); ?></h1>
	</header>

	<?php if ( have_posts() ) : ?>
		<div class="hoptown-inflatable-grid">
			<?php
			while ( have_posts() ) :
				the_post();
				$inflatable    = Hoptown_Rental_Inflatable::from_id( get_the_ID() );
				$base_price    = $inflatable->base_price;
				$gallery_ids   = $inflatable->gallery_ids ? implode( ',', $inflatable->gallery_ids ) : '';
				$gallery_image = "";

				if ( ! empty( $gallery_ids ) ) {
					$gallery_list  = explode( ",", $gallery_ids );
					$first_image   = reset( $gallery_list );
					$gallery_image = $first_image ? wp_get_attachment_image_url( $first_image, "medium" ) : "";
				}
				?>
				<article id="post-<?php the_ID(); ?>" <?php post_class( "hoptown-inflatable-card" ); ?>>
					<a class="hoptown-inflatable-link" href="<?php the_permalink(); ?>">
						<?php if ( has_post_thumbnail() ) : ?>
							<?php the_post_thumbnail( "medium" ); ?>
						<?php elseif ( $gallery_image ) : ?>
							<img src="<?php echo esc_url( $gallery_image ); ?>" alt="<?php the_title_attribute(); ?>" />
						<?php endif; ?>
						<h2 class="hoptown-inflatable-title"><?php the_title(); ?></h2>
					</a>

					<?php if ( has_excerpt() ) : ?>
						<div class="hoptown-inflatable-excerpt">
							<?php the_excerpt(); ?>
						</div>
					<?php endif; ?>

					<?php if ( $base_price !== "" ) : ?>
						<p class="hoptown-inflatable-price">
							<?php echo esc_html( ( new Hoptown_Rental_Money( $base_price ) )->format() ); ?>
						</p>
					<?php endif; ?>
				</article>
			<?php endwhile; ?>
		</div>

		<?php the_posts_navigation(); ?>
	<?php else : ?>
		<p><?php esc_html_e( "No inflatables found.", HOPTOWN_RENTAL_TEXTDOMAIN ); ?></p>
	<?php endif; ?>
</main>
<?php

get_footer();
