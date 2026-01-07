<?php
/**
 * Meta boxes for Inflatable post type
 *
 * @package    Hoptown_Rental
 * @subpackage Hoptown_Rental/includes/admin
 */

class Hoptown_Rental_Inflatable_Meta_Boxes {

	/**
	 * Add meta boxes.
	 */
	public function add_meta_boxes() {
		add_meta_box(
			'hoptown_pricing',
			__( 'Pricing', HOPTOWN_RENTAL_TEXTDOMAIN ),
			array( $this, 'render_pricing_meta_box' ),
			Hoptown_Rental_Inflatable_Post_Type::POST_TYPE,
			'normal',
			'high'
		);

		add_meta_box(
			'hoptown_delivery',
			__( 'Delivery Options', HOPTOWN_RENTAL_TEXTDOMAIN ),
			array( $this, 'render_delivery_meta_box' ),
			Hoptown_Rental_Inflatable_Post_Type::POST_TYPE,
			'normal',
			'high'
		);

		add_meta_box(
			'hoptown_gallery',
			__( 'Gallery', HOPTOWN_RENTAL_TEXTDOMAIN ),
			array( $this, 'render_gallery_meta_box' ),
			Hoptown_Rental_Inflatable_Post_Type::POST_TYPE,
			'side',
			'default'
		);
	}

	/**
	 * Render pricing meta box.
	 *
	 * @param WP_Post $post Current post object.
	 */
	public function render_pricing_meta_box( $post ) {
		wp_nonce_field( 'hoptown_pricing_meta_box', 'hoptown_pricing_nonce' );

		$base_price      = Hoptown_Rental_Meta::get( $post->ID, Hoptown_Rental_Meta::INFLATABLE_BASE_PRICE );
		$use_day_pricing = Hoptown_Rental_Meta::get( $post->ID, Hoptown_Rental_Meta::INFLATABLE_USE_DAY_PRICING );
		$weekday_price   = Hoptown_Rental_Meta::get( $post->ID, Hoptown_Rental_Meta::INFLATABLE_WEEKDAY_PRICE );
		$weekend_price   = Hoptown_Rental_Meta::get( $post->ID, Hoptown_Rental_Meta::INFLATABLE_WEEKEND_PRICE );
		?>
		<table class="form-table">
			<tr>
				<th scope="row">
					<label for="hoptown_base_price"><?php esc_html_e( 'Base Price (€)', HOPTOWN_RENTAL_TEXTDOMAIN ); ?></label>
				</th>
				<td>
					<input type="number" step="0.01" id="hoptown_base_price" name="hoptown_base_price" value="<?php echo esc_attr( $base_price ); ?>" class="regular-text" />
					<p class="description"><?php esc_html_e( 'Default rental price per day', HOPTOWN_RENTAL_TEXTDOMAIN ); ?></p>
				</td>
			</tr>
			<tr>
				<th scope="row">
					<label for="hoptown_use_day_pricing"><?php esc_html_e( 'Use Day-Specific Pricing', HOPTOWN_RENTAL_TEXTDOMAIN ); ?></label>
				</th>
				<td>
					<input type="checkbox" id="hoptown_use_day_pricing" name="hoptown_use_day_pricing" value="yes" <?php checked( $use_day_pricing, 'yes' ); ?> />
					<p class="description"><?php esc_html_e( 'Enable different prices for weekdays and weekends', HOPTOWN_RENTAL_TEXTDOMAIN ); ?></p>
				</td>
			</tr>
			<tr class="hoptown-day-pricing" style="<?php echo ( 'yes' !== $use_day_pricing ) ? 'display:none;' : ''; ?>">
				<th scope="row">
					<label for="hoptown_weekday_price"><?php esc_html_e( 'Weekday Price (€)', HOPTOWN_RENTAL_TEXTDOMAIN ); ?></label>
				</th>
				<td>
					<input type="number" step="0.01" id="hoptown_weekday_price" name="hoptown_weekday_price" value="<?php echo esc_attr( $weekday_price ); ?>" class="regular-text" />
					<p class="description"><?php esc_html_e( 'Price for Monday-Friday', HOPTOWN_RENTAL_TEXTDOMAIN ); ?></p>
				</td>
			</tr>
			<tr class="hoptown-day-pricing" style="<?php echo ( 'yes' !== $use_day_pricing ) ? 'display:none;' : ''; ?>">
				<th scope="row">
					<label for="hoptown_weekend_price"><?php esc_html_e( 'Weekend Price (€)', HOPTOWN_RENTAL_TEXTDOMAIN ); ?></label>
				</th>
				<td>
					<input type="number" step="0.01" id="hoptown_weekend_price" name="hoptown_weekend_price" value="<?php echo esc_attr( $weekend_price ); ?>" class="regular-text" />
					<p class="description"><?php esc_html_e( 'Price for Saturday-Sunday', HOPTOWN_RENTAL_TEXTDOMAIN ); ?></p>
				</td>
			</tr>
		</table>

		<script>
		jQuery(document).ready(function($) {
			$('#hoptown_use_day_pricing').on('change', function() {
				if ($(this).is(':checked')) {
					$('.hoptown-day-pricing').show();
				} else {
					$('.hoptown-day-pricing').hide();
				}
			});
		});
		</script>
		<?php
	}

	/**
	 * Render delivery meta box.
	 *
	 * @param WP_Post $post Current post object.
	 */
	public function render_delivery_meta_box( $post ) {
		wp_nonce_field( 'hoptown_delivery_meta_box', 'hoptown_delivery_nonce' );

		$delivery_price = Hoptown_Rental_Meta::get( $post->ID, Hoptown_Rental_Meta::INFLATABLE_DELIVERY_PRICE );
		?>
		<table class="form-table">
			<tr>
				<th scope="row">
					<label for="hoptown_delivery_price"><?php esc_html_e( 'Delivery Price (€)', HOPTOWN_RENTAL_TEXTDOMAIN ); ?></label>
				</th>
				<td>
					<input type="number" step="0.01" id="hoptown_delivery_price" name="hoptown_delivery_price" value="<?php echo esc_attr( $delivery_price ); ?>" class="regular-text" />
					<p class="description"><?php esc_html_e( 'Additional charge for delivery', HOPTOWN_RENTAL_TEXTDOMAIN ); ?></p>
				</td>
			</tr>
		</table>
		<?php
	}

	/**
	 * Render gallery meta box.
	 *
	 * @param WP_Post $post Current post object.
	 */
	public function render_gallery_meta_box( $post ) {
		wp_nonce_field( 'hoptown_gallery_meta_box', 'hoptown_gallery_nonce' );

		$gallery_ids = Hoptown_Rental_Meta::get( $post->ID, Hoptown_Rental_Meta::INFLATABLE_GALLERY );
		$gallery_ids = ! empty( $gallery_ids ) ? explode( ',', $gallery_ids ) : array();
		?>
		<div class="hoptown-gallery-container">
			<ul class="hoptown-gallery-images">
				<?php
				if ( ! empty( $gallery_ids ) ) {
					foreach ( $gallery_ids as $image_id ) {
						$image_url = wp_get_attachment_image_src( $image_id, 'thumbnail' );
						if ( $image_url ) {
							?>
							<li data-id="<?php echo esc_attr( $image_id ); ?>">
								<img src="<?php echo esc_url( $image_url[0] ); ?>" />
								<a href="#" class="hoptown-remove-gallery-image">&times;</a>
							</li>
							<?php
						}
					}
				}
				?>
			</ul>
			<input type="hidden" id="hoptown_gallery" name="hoptown_gallery" value="<?php echo esc_attr( implode( ',', $gallery_ids ) ); ?>" />
			<button type="button" class="button hoptown-add-gallery-images"><?php esc_html_e( 'Add Images', HOPTOWN_RENTAL_TEXTDOMAIN ); ?></button>
		</div>

		<style>
		.hoptown-gallery-images {
			list-style: none;
			margin: 0;
			padding: 0;
			display: flex;
			flex-wrap: wrap;
			gap: 10px;
		}
		.hoptown-gallery-images li {
			position: relative;
			display: inline-block;
		}
		.hoptown-gallery-images img {
			width: 100px;
			height: 100px;
			object-fit: cover;
			border: 1px solid #ddd;
		}
		.hoptown-remove-gallery-image {
			position: absolute;
			top: -5px;
			right: -5px;
			background: red;
			color: white;
			border-radius: 50%;
			width: 20px;
			height: 20px;
			text-align: center;
			line-height: 20px;
			text-decoration: none;
			font-weight: bold;
		}
		</style>

		<script>
		jQuery(document).ready(function($) {
			var frame;

			$('.hoptown-add-gallery-images').on('click', function(e) {
				e.preventDefault();

				if (frame) {
					frame.open();
					return;
				}

				frame = wp.media({
					title: '<?php esc_html_e( 'Select Gallery Images', HOPTOWN_RENTAL_TEXTDOMAIN ); ?>',
					button: {
						text: '<?php esc_html_e( 'Add to Gallery', HOPTOWN_RENTAL_TEXTDOMAIN ); ?>'
					},
					multiple: true
				});

				frame.on('select', function() {
					var attachments = frame.state().get('selection').toJSON();
					var galleryInput = $('#hoptown_gallery');
					var currentIds = galleryInput.val() ? galleryInput.val().split(',') : [];
					var galleryList = $('.hoptown-gallery-images');

					$.each(attachments, function(index, attachment) {
						if (currentIds.indexOf(String(attachment.id)) === -1) {
							currentIds.push(attachment.id);
							var thumbnail = attachment.sizes && attachment.sizes.thumbnail ? attachment.sizes.thumbnail.url : attachment.url;
							galleryList.append('<li data-id="' + attachment.id + '"><img src="' + thumbnail + '" /><a href="#" class="hoptown-remove-gallery-image">&times;</a></li>');
						}
					});

					galleryInput.val(currentIds.join(','));
				});

				frame.open();
			});

			$(document).on('click', '.hoptown-remove-gallery-image', function(e) {
				e.preventDefault();
				var $li = $(this).closest('li');
				var imageId = $li.data('id');
				var galleryInput = $('#hoptown_gallery');
				var currentIds = galleryInput.val().split(',');
				var newIds = currentIds.filter(function(id) {
					return String(id) !== String(imageId);
				});

				galleryInput.val(newIds.join(','));
				$li.remove();
			});
		});
		</script>
		<?php
	}

	/**
	 * Save meta boxes.
	 *
	 * @param int     $post_id Post ID.
	 * @param WP_Post $post    Post object.
	 */
	public function save_meta_boxes( $post_id, $post ) {
		// Check if it's the correct post type
		if ( Hoptown_Rental_Inflatable_Post_Type::POST_TYPE !== $post->post_type ) {
			return;
		}

		// Check autosave
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}

		// Check permissions
		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return;
		}

		// Save pricing fields
		if ( isset( $_POST['hoptown_pricing_nonce'] ) && wp_verify_nonce( $_POST['hoptown_pricing_nonce'], 'hoptown_pricing_meta_box' ) ) {
			Hoptown_Rental_Inflatable_Service::save_from_admin( $post_id, $_POST );
		}

		// Save delivery fields
		if ( isset( $_POST['hoptown_delivery_nonce'] ) && wp_verify_nonce( $_POST['hoptown_delivery_nonce'], 'hoptown_delivery_meta_box' ) ) {
			Hoptown_Rental_Inflatable_Service::save_from_admin( $post_id, $_POST );
		}

		// Save gallery
		if ( isset( $_POST['hoptown_gallery_nonce'] ) && wp_verify_nonce( $_POST['hoptown_gallery_nonce'], 'hoptown_gallery_meta_box' ) ) {
			Hoptown_Rental_Inflatable_Service::save_from_admin( $post_id, $_POST );
		}
	}
}
