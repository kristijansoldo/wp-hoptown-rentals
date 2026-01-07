# Hoptown Rental - Setup Instructions

## Prerequisites

Before you begin, ensure you have the following installed:
- Docker Desktop (Windows/Mac) or Docker Engine + Docker Compose (Linux)
- Web browser (Chrome, Firefox, Safari, or Edge)

## Step-by-Step Setup

### Step 1: Start Docker Environment

Open terminal/command prompt in the project directory and run:

```bash
docker-compose up -d
```

This command will:
- Download MySQL and WordPress Docker images (first time only)
- Create containers for MySQL and WordPress
- Mount the plugin directory
- Start the services

**Wait 1-2 minutes** for services to fully start.

### Step 2: Access WordPress Installation

1. Open your web browser
2. Go to: **http://localhost:8080**
3. You should see the WordPress installation screen

### Step 3: Install WordPress

Fill in the WordPress installation form:

- **Site Title**: Hoptown Rental
- **Username**: admin (or your preferred username)
- **Password**: Choose a strong password
- **Your Email**: your-email@example.com
- **Search Engine Visibility**: Uncheck (for development)

Click **Install WordPress**

### Step 4: Login to WordPress Admin

After installation, login with your credentials:
- URL: http://localhost:8080/wp-admin
- Username: (what you entered in Step 3)
- Password: (what you entered in Step 3)

### Step 5: Activate the Plugin

1. In WordPress admin, go to **Plugins** menu
2. Find "Hoptown Rental" in the list
3. Click **Activate**

### Step 6: Create Your First Inflatable

1. In WordPress admin, click **Inflatables** → **Add New**
2. Enter inflatable details:
   - **Title**: e.g., "Super Bouncy Castle"
   - **Description**: Add description in the editor
   - **Featured Image**: Click "Set featured image" and upload main image
3. Scroll down to **Pricing** section:
   - Base Price: 50
   - Check "Use Day-Specific Pricing" (optional)
   - Weekday Price: 30
   - Weekend Price: 70
4. Scroll to **Delivery Options**:
   - Delivery Price: 15
5. Scroll to **Gallery**:
   - Click "Add Images" and select multiple images
6. Click **Publish**

### Step 7: Test the Booking System

#### Option A: Using Shortcodes (Quick Test)

1. Go to **Pages** → **Add New**
2. Title: "Book a Bouncy Castle"
3. Add a Paragraph block and switch to code editor
4. Paste this code (replace `INFLATABLE_ID` with your inflatable's ID):
```
[hoptown_booking_calendar inflatable_id="INFLATABLE_ID"]
[hoptown_booking_form inflatable_id="INFLATABLE_ID"]
```
5. To find your inflatable ID:
   - Go to **Inflatables** → **All Inflatables**
   - Hover over your inflatable title
   - Look at the browser status bar at the bottom - the number after `post=` is your ID
6. Click **Publish**
7. Click **View Page** to see the booking calendar

#### Option B: Using Theme Templates (Advanced)

See README.md for theme integration examples.

### Step 8: Test a Booking

1. Go to the page you created (from Step 7)
2. Click on an available date in the calendar
3. Fill in the booking form:
   - Name and Surname
   - Email
   - Phone
   - Note (optional)
   - Select Delivery or Pickup
4. Click **Reserve**
5. Check **Bookings** in admin to see the booking

## Stopping the Environment

To stop the Docker containers:

```bash
docker-compose down
```

To stop and remove all data:

```bash
docker-compose down -v
```

## Restarting the Environment

To restart after stopping:

```bash
docker-compose up -d
```

Your data will be preserved in Docker volumes.

## Accessing the Database (Optional)

If you need to access the database directly:

**MySQL Connection Details:**
- Host: localhost
- Port: 3306
- Database: wordpress
- Username: wordpress
- Password: wordpress123
- Root Password: rootpassword123

You can use tools like:
- phpMyAdmin (install separately)
- MySQL Workbench
- DBeaver
- Command line: `docker exec -it hoptown_mysql mysql -u wordpress -p`

## Common Issues

### Issue: Port 8080 already in use

**Solution:**
Edit `docker-compose.yml` and change the port:
```yaml
ports:
  - "8081:80"  # Changed from 8080 to 8081
```
Then access WordPress at http://localhost:8081

### Issue: Port 3306 already in use

**Solution:**
Edit `docker-compose.yml`:
```yaml
ports:
  - "3307:3306"  # Changed from 3306 to 3307
```

### Issue: "Cannot connect to database"

**Solution:**
Wait 1-2 minutes for MySQL to fully start, then refresh the page.

### Issue: Plugin not showing up

**Solution:**
1. Check that the plugin directory is mounted correctly
2. Run: `docker-compose down && docker-compose up -d`
3. Check file permissions

### Issue: Changes not reflecting

**Solution:**
1. Clear browser cache (Ctrl+Shift+R or Cmd+Shift+R)
2. Disable WordPress caching plugins
3. Check if files are saved correctly

### Issue: 404 on archive/single URLs

**Solution:**
1. Go to **Settings → Permalinks** and click **Save**
2. If site language is Croatian, use `/napuhanci/` instead of `/inflatables/`

## Checking Logs

To view WordPress logs:
```bash
docker-compose logs wordpress
```

To view MySQL logs:
```bash
docker-compose logs mysql
```

To follow logs in real-time:
```bash
docker-compose logs -f
```

## Next Steps

After setup, refer to README.md for:
- Detailed usage instructions
- Theme integration examples
- REST API documentation
- Customization options

## Development Tips

1. **Live Editing**: Changes to plugin files are immediately reflected (no need to restart Docker)
2. **Database Persistence**: Data is stored in Docker volumes and persists between restarts
3. **Clean Start**: Use `docker-compose down -v` to remove all data and start fresh
4. **Multiple Environments**: Copy the entire directory to run multiple instances (change ports)

## Need Help?

If you encounter issues not covered here:
1. Check WordPress debug.log (`wp-content/debug.log`)
2. Check browser console for JavaScript errors
3. Review README.md for advanced topics
4. Contact your development team

---

Happy booking!
