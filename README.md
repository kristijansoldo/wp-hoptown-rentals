# Hoptown Rental

WordPress plugin for managing inflatable rentals with booking calendar, pricing rules, and delivery options.

## Features
- Inflatables + bookings custom post types
- Booking calendar + booking form with availability checks
- Pricing rules (base/weekday/weekend) and delivery pricing
- Croatian localization and HR slug (`/napuhanci/`)
- Email notifications on new bookings
- Admin settings under **Inflatables → Settings**

## Install (Production)
1. Download the latest release zip from GitHub Releases.
2. In WordPress Admin → Plugins → **Add New** → **Upload Plugin**.
3. Upload the zip, install, and activate **Hoptown Rental**.

## Development / Contribute
### Prerequisites
- Docker + Docker Compose

### Run locally
```bash
docker compose up -d
```

Then open `http://localhost:8080`, finish the WP installer, and activate the plugin.

### Helpful URLs
- Inflatables archive: `http://localhost:8080/inflatables/` (or `/napuhanci/` in HR)
- Single inflatable: click from archive

### Booking UI (shortcodes)
```text
[hoptown_booking_calendar inflatable_id="123"]
[hoptown_booking_form inflatable_id="123"]
```

### Template overrides
You can override plugin templates by placing copies in your active theme:
- `your-theme/hoptown-rental/archive-hoptown_inflatable.php`
- `your-theme/hoptown-rental/single-hoptown_inflatable.php`

## License
GPL-2.0+
