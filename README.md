# Hoptown Rental - WordPress Plugin for Inflatable Rentals

Complete WordPress plugin for managing inflatable rentals with booking calendar, pricing rules, and delivery options.

## Quick Start

### Prerequisites
- Docker and Docker Compose installed
- Git (optional)

### Installation

1. **Start the Docker environment:**
```bash
docker-compose up -d
```

2. **Access WordPress:**
   - URL: http://localhost:8080
   - Follow WordPress installation wizard
   - Create admin account

3. **Activate the plugin:**
   - Go to WordPress Admin → Plugins
   - Activate "Hoptown Rental"

## Plugin Structure

```
wp-hoptown-rental/
├── wp-hoptown-rental.php          # Main plugin file
├── includes/
│   ├── class-hoptown-rental.php   # Core plugin class
│   ├── class-activator.php        # Activation handler
│   ├── class-deactivator.php      # Deactivation handler
│   ├── post-types/                # Custom Post Types
│   │   ├── class-inflatable-post-type.php
│   │   └── class-booking-post-type.php
│   ├── admin/                     # Admin functionality
│   │   ├── class-admin.php
│   │   ├── class-inflatable-meta-boxes.php
│   │   └── class-booking-meta-boxes.php
│   ├── public/                    # Public-facing functionality
│   │   ├── class-public.php
│   │   └── class-booking-handler.php
│   └── api/                       # REST API
│       └── class-booking-api.php
├── assets/
│   ├── css/                       # Stylesheets
│   │   ├── admin.css
│   │   └── public.css
│   └── js/                        # JavaScript files
│       ├── admin.js
│       └── booking.js
└── templates/                     # Template files
    ├── archive-hoptown_inflatable.php
    ├── single-hoptown_inflatable.php
    ├── booking-calendar.php
    └── booking-form.php
```

## Features

### Localization
- Built-in Croatian translations for admin and frontend
- Slug switches to `/napuhanci/` when site language is Croatian

### Admin Features
- Create and manage inflatables
- Set base price and day-specific pricing (weekday/weekend)
- Configure delivery pricing
- Upload featured image and gallery
- View and manage all bookings
- Full booking details in admin panel

### Customer Features
- Interactive calendar with availability
- Real-time pricing based on selected date
- Choose delivery or pickup
- Complete booking form with validation
- Automatic booking confirmation

## Usage Guide

### 1. Adding an Inflatable (Admin)

1. Go to **Inflatables → Add New**
2. Enter inflatable title and description
3. Set **Featured Image** (main image)
4. In **Gallery** section, add multiple images
5. In **Pricing** section:
   - Set base price (€)
   - Optionally enable day-specific pricing:
     - Check "Use Day-Specific Pricing"
     - Set weekday price (Monday-Friday)
     - Set weekend price (Saturday-Sunday)
6. In **Delivery Options** section:
   - Set delivery price (€)
7. Click **Publish**

### 2. Displaying Inflatables in Your Theme

This plugin ships with built-in archive and single templates for inflatables.
If you want to override them, place copies in your theme under:
`/hoptown-rental/archive-hoptown_inflatable.php` and
`/hoptown-rental/single-hoptown_inflatable.php`.

#### Method 1: Using Shortcodes (Recommended for beginners)

Add this code to any page or post to display the booking system:

```php
[hoptown_booking_calendar inflatable_id="123"]
[hoptown_booking_form inflatable_id="123"]
```

Replace `123` with your actual inflatable ID.

#### Method 2: Custom Theme Integration (Advanced)

**A. Display list of inflatables:**

```php
<?php
// In your theme template file (e.g., page-inflatables.php)
$args = array(
    'post_type'      => 'hoptown_inflatable',
    'posts_per_page' => -1,
    'orderby'        => 'title',
    'order'          => 'ASC',
);

$inflatables = new WP_Query( $args );

if ( $inflatables->have_posts() ) :
    while ( $inflatables->have_posts() ) : $inflatables->the_post();
        ?>
        <div class="inflatable-item">
            <h2><?php the_title(); ?></h2>

            <?php if ( has_post_thumbnail() ) : ?>
                <?php the_post_thumbnail( 'large' ); ?>
            <?php endif; ?>

            <div class="inflatable-content">
                <?php the_content(); ?>
            </div>

            <?php
            // Get pricing info
            $base_price = get_post_meta( get_the_ID(), '_hoptown_base_price', true );
            $use_day_pricing = get_post_meta( get_the_ID(), '_hoptown_use_day_pricing', true );
            $weekday_price = get_post_meta( get_the_ID(), '_hoptown_weekday_price', true );
            $weekend_price = get_post_meta( get_the_ID(), '_hoptown_weekend_price', true );
            ?>

            <div class="inflatable-pricing">
                <?php if ( 'yes' === $use_day_pricing ) : ?>
                    <p>Weekday Price: <?php echo esc_html( number_format( $weekday_price, 2 ) ); ?> €</p>
                    <p>Weekend Price: <?php echo esc_html( number_format( $weekend_price, 2 ) ); ?> €</p>
                <?php else : ?>
                    <p>Price: <?php echo esc_html( number_format( $base_price, 2 ) ); ?> €</p>
                <?php endif; ?>
            </div>

            <a href="<?php the_permalink(); ?>" class="btn">Book Now</a>
        </div>
        <?php
    endwhile;
    wp_reset_postdata();
endif;
?>
```

**B. Display single inflatable with booking system:**

The plugin already provides a single template. If you want a custom one,
create `single-hoptown_inflatable.php` in your theme or under
`/hoptown-rental/` and adjust as needed:

```php
<?php
/**
 * Single Inflatable Template
 */

get_header();

while ( have_posts() ) : the_post();
    ?>
    <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
        <h1><?php the_title(); ?></h1>

        <?php if ( has_post_thumbnail() ) : ?>
            <div class="inflatable-featured-image">
                <?php the_post_thumbnail( 'large' ); ?>
            </div>
        <?php endif; ?>

        <div class="inflatable-description">
            <?php the_content(); ?>
        </div>

        <?php
        // Display gallery
        $gallery_ids = get_post_meta( get_the_ID(), '_hoptown_gallery', true );
        if ( $gallery_ids ) :
            $gallery_ids = explode( ',', $gallery_ids );
            ?>
            <div class="inflatable-gallery">
                <h3>Gallery</h3>
                <div class="gallery-grid">
                    <?php foreach ( $gallery_ids as $image_id ) :
                        $image_url = wp_get_attachment_image_url( $image_id, 'medium' );
                        if ( $image_url ) :
                            ?>
                            <img src="<?php echo esc_url( $image_url ); ?>" alt="" />
                        <?php
                        endif;
                    endforeach; ?>
                </div>
            </div>
        <?php endif; ?>

        <!-- Booking Calendar -->
        <div class="booking-section">
            <h2>Book This Inflatable</h2>
            <?php echo do_shortcode( '[hoptown_booking_calendar inflatable_id="' . get_the_ID() . '"]' ); ?>
            <?php echo do_shortcode( '[hoptown_booking_form inflatable_id="' . get_the_ID() . '"]' ); ?>
        </div>
    </article>
    <?php
endwhile;

get_footer();
?>
```

**C. Display bookings in admin (for custom reporting):**

```php
<?php
// Get all bookings for a specific inflatable
$args = array(
    'post_type'      => 'hoptown_booking',
    'posts_per_page' => -1,
    'meta_query'     => array(
        array(
            'key'     => '_hoptown_inflatable_id',
            'value'   => 123, // Your inflatable ID
            'compare' => '=',
        ),
    ),
);

$bookings = new WP_Query( $args );

if ( $bookings->have_posts() ) :
    while ( $bookings->have_posts() ) : $bookings->the_post();
        $booking_date = get_post_meta( get_the_ID(), '_hoptown_booking_date', true );
        $customer_name = get_post_meta( get_the_ID(), '_hoptown_customer_name', true );
        $total_price = get_post_meta( get_the_ID(), '_hoptown_total_price', true );

        echo '<div>';
        echo 'Date: ' . esc_html( $booking_date ) . '<br>';
        echo 'Customer: ' . esc_html( $customer_name ) . '<br>';
        echo 'Total: ' . esc_html( number_format( $total_price, 2 ) ) . ' €<br>';
        echo '</div>';
    endwhile;
    wp_reset_postdata();
endif;
?>
```

### 3. Using REST API

The plugin provides REST API endpoints for checking availability and pricing:

**Get availability for an inflatable:**
```
GET /wp-json/hoptown-rental/v1/availability/{inflatable_id}
```

**Get price for a specific date:**
```
GET /wp-json/hoptown-rental/v1/price/{inflatable_id}?date=2024-12-25
```

## Customizing Styles

You can override plugin styles by adding CSS to your theme:

```css
/* Override calendar styles */
.hoptown-booking-calendar {
    /* Your custom styles */
}

/* Override form styles */
.hoptown-booking-form-wrapper {
    /* Your custom styles */
}
```

## Helper Functions

### Check if date is available
```php
$is_available = Hoptown_Rental_Inflatable_Post_Type::is_available( $inflatable_id, '2024-12-25' );
```

### Get price for date
```php
$price = Hoptown_Rental_Inflatable_Post_Type::get_price_for_date( $inflatable_id, '2024-12-25' );
```

### Get all booked dates
```php
$booked_dates = Hoptown_Rental_Booking_Post_Type::get_booked_dates( $inflatable_id );
```

### Create booking programmatically
```php
$booking_data = array(
    'inflatable_id'    => 123,
    'booking_date'     => '2024-12-25',
    'customer_name'    => 'John Doe',
    'customer_email'   => 'john@example.com',
    'customer_phone'   => '+1234567890',
    'customer_note'    => 'Need setup after 5 PM.',
    'delivery_method'  => 'delivery', // or 'pickup'
    'delivery_address' => '123 Main St, City',
    'pickup_time'      => '14:00', // if pickup
);

$booking_id = Hoptown_Rental_Booking_Post_Type::create_booking( $booking_data );
```

## Database Structure

The plugin uses WordPress Custom Post Types - no custom database tables needed!

**Post Types:**
- `hoptown_inflatable` - Inflatables
- `hoptown_booking` - Bookings

**Meta Keys (Inflatables):**
- `_hoptown_base_price` - Base rental price
- `_hoptown_use_day_pricing` - Enable day-specific pricing (yes/no)
- `_hoptown_weekday_price` - Weekday price
- `_hoptown_weekend_price` - Weekend price
- `_hoptown_delivery_price` - Delivery charge
- `_hoptown_gallery` - Comma-separated gallery image IDs

**Meta Keys (Bookings):**
- `_hoptown_inflatable_id` - Related inflatable ID
- `_hoptown_booking_date` - Booking date (Y-m-d)
- `_hoptown_customer_name` - Customer name
- `_hoptown_customer_email` - Customer email
- `_hoptown_customer_phone` - Customer phone
- `_hoptown_customer_note` - Customer note
- `_hoptown_delivery_method` - delivery or pickup
- `_hoptown_delivery_address` - Delivery address
- `_hoptown_pickup_time` - Pickup time
- `_hoptown_rental_price` - Rental price
- `_hoptown_delivery_price` - Delivery price
- `_hoptown_total_price` - Total price

## Troubleshooting

### Calendar not showing
1. Make sure you've added the shortcode with correct inflatable_id
2. Check browser console for JavaScript errors
3. Verify jQuery is loaded

### Bookings not saving
1. Check PHP error logs
2. Verify WordPress AJAX is working
3. Check nonce verification

### Styles not loading
1. Clear browser cache
2. Clear WordPress cache (if using caching plugin)
3. Check if CSS files exist in `assets/css/`

## Security

- All inputs are sanitized and validated
- Nonce verification for all AJAX requests
- REST API endpoints use WordPress permissions
- SQL injection prevention through WordPress APIs
- XSS protection through proper escaping

## Development

To modify the plugin:

1. Edit files in `wp-hoptown-rental/` directory
2. Changes are reflected immediately (volume mounted)
3. Test thoroughly before deploying to production

## Deployment

To deploy to production:

1. Copy the `wp-hoptown-rental/` directory to your production site's `wp-content/plugins/` directory
2. Activate the plugin
3. Configure inflatables and settings

## License

GPL-2.0+

## Support

For issues or questions, please contact your development team.

---

Made for Hoptown
