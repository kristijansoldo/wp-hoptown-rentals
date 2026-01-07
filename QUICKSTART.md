# Quick Start Guide

Quick guide to get Hoptown Rental plugin up and running.

## 3 Steps to Your First Booking

### Step 1: Start Docker
```bash
docker-compose up -d
```
Wait 1-2 minutes for services to start.

### Step 2: Install WordPress
1. Open: **http://localhost:8080**
2. Complete WordPress installation
3. Login to admin panel
4. Go to **Plugins** → Activate **"Hoptown Rental"**

### Step 3: Add an Inflatable and Test
1. **Inflatables** → **Add New**
   - Title: "Super Bouncy Castle"
   - Set featured image
   - Base Price: 50
   - Delivery Price: 15
   - **Publish**

2. **Pages** → **Add New**
   - Title: "Book Now"
   - In editor add:
   ```
   [hoptown_booking_calendar inflatable_id="1"]
   [hoptown_booking_form inflatable_id="1"]
   ```
   - **Publish**

3. Click **View Page** and test booking!

## More Details

- **SETUP.md** - Step-by-step setup guide
- **README.md** - Complete documentation
- **GUTENBERG.md** - Gutenberg block integration (optional)

## Stop Docker

```bash
docker-compose down
```

## What's Included

- Docker Compose with MySQL and WordPress
- OOP WordPress plugin
- Custom Post Types (Inflatables + Bookings)
- Admin interface (pricing, delivery, rules)
- Interactive calendar
- Booking form
- Automatic availability validation
- REST API endpoints
- Complete documentation

Enjoy!
