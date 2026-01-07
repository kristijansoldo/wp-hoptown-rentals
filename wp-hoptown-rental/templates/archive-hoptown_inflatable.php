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
				$base_price    = get_post_meta( get_the_ID(), "_hoptown_base_price", true );
				$gallery_ids   = get_post_meta( get_the_ID(), "_hoptown_gallery", true );
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
							<?php echo esc_html( number_format( (float) $base_price, 2 ) ); ?> €
						</p>
					<?php endif; ?>
				</article>
			<?php endwhile; ?>
		</div>

		<?php the_posts_navigation(); ?>
	<?php else : ?>
		<p><?php esc_html_e( "No inflatables found.", "hoptown-rental" ); ?></p>
	<?php endif; ?>
</main>
<?php

get_footer();
