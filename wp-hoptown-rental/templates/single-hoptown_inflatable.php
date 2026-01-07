<?php
/**
 * Single Inflatable Template
 *
 * @package Hoptown_Rental
 */

get_header();

while ( have_posts() ) :
	the_post();
	$inflatable  = Hoptown_Rental_Inflatable::from_id( get_the_ID() );
	$base_price  = $inflatable->base_price;
	$gallery_ids = $inflatable->gallery_ids ? implode( ',', $inflatable->gallery_ids ) : '';
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
					<?php echo esc_html( ( new Hoptown_Rental_Money( $base_price ) )->format() ); ?>
				</p>
			<?php endif; ?>

			<div class="inflatable-description">
				<?php the_content(); ?>
			</div>

			<?php if ( $gallery_ids ) : ?>
				<?php $gallery_list = explode( ",", $gallery_ids ); ?>
				<div class="inflatable-gallery">
					<h3><?php esc_html_e( "Gallery", HOPTOWN_RENTAL_TEXTDOMAIN ); ?></h3>
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
				<h2><?php esc_html_e( "Book This Inflatable", HOPTOWN_RENTAL_TEXTDOMAIN ); ?></h2>
				<?php echo do_shortcode( '[hoptown_booking_calendar inflatable_id="' . get_the_ID() . '"]' ); ?>
				<?php echo do_shortcode( '[hoptown_booking_form inflatable_id="' . get_the_ID() . '"]' ); ?>
			</div>
		</article>
	</main>
<?php endwhile; ?>

<?php get_footer(); ?>
