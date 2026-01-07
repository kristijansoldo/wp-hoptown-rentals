<?php
/**
 * Single Inflatable Template
 *
 * @package Hoptown_Rental
 */

get_header();

while ( have_posts() ) :
	the_post();
	$base_price  = get_post_meta( get_the_ID(), "_hoptown_base_price", true );
	$gallery_ids = get_post_meta( get_the_ID(), "_hoptown_gallery", true );
	?>
	<main id="primary" class="site-main hoptown-inflatable-single">
		<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
			<h1 class="entry-title"><?php the_title(); ?></h1>

			<?php if ( has_post_thumbnail() ) : ?>
				<div class="inflatable-featured-image">
					<?php the_post_thumbnail( "large" ); ?>
				</div>
			<?php endif; ?>

			<?php if ( $base_price !== "" ) : ?>
				<p class="hoptown-inflatable-price">
					<?php echo esc_html( number_format( (float) $base_price, 2 ) ); ?> €
				</p>
			<?php endif; ?>

			<div class="inflatable-description">
				<?php the_content(); ?>
			</div>

			<?php if ( $gallery_ids ) : ?>
				<?php $gallery_list = explode( ",", $gallery_ids ); ?>
				<div class="inflatable-gallery">
					<h3><?php esc_html_e( "Gallery", "hoptown-rental" ); ?></h3>
					<div class="gallery-grid">
						<?php foreach ( $gallery_list as $image_id ) : ?>
							<?php $image_url = wp_get_attachment_image_url( $image_id, "medium" ); ?>
							<?php if ( $image_url ) : ?>
								<img src="<?php echo esc_url( $image_url ); ?>" alt="" />
							<?php endif; ?>
						<?php endforeach; ?>
					</div>
				</div>
			<?php endif; ?>

			<div class="booking-section">
				<h2><?php esc_html_e( "Book This Inflatable", "hoptown-rental" ); ?></h2>
				<?php echo do_shortcode( '[hoptown_booking_calendar inflatable_id="' . get_the_ID() . '"]' ); ?>
				<?php echo do_shortcode( '[hoptown_booking_form inflatable_id="' . get_the_ID() . '"]' ); ?>
			</div>
		</article>
	</main>
<?php endwhile; ?>

<?php get_footer(); ?>
