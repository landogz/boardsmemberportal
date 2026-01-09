# Coming Soon & Maintenance Mode Setup

## Overview
Both the coming soon page and maintenance page are beautiful, animated landing pages that display when the website is not yet launched or under maintenance. They feature countdown timers and follow the Board Member Portal brand colors.

**Note:** Maintenance mode takes priority over coming soon mode. If both are enabled, maintenance mode will be shown.

## Configuration

### Step 1: Add to .env file

Add these lines to your `.env` file:

```env
# Maintenance Mode (takes priority over coming soon)
# Set to true to show the maintenance page, false to show normal landing page
MAINTENANCE_MODE_ENABLED=false

# Maintenance end date for the countdown timer (format: YYYY-MM-DD HH:MM:SS)
MAINTENANCE_END_DATE=2026-01-20 12:00:00

# Coming Soon Mode
# Set to true to show the coming soon page, false to show normal landing page
COMING_SOON_ENABLED=false

# Launch date for the countdown timer (format: YYYY-MM-DD)
COMING_SOON_LAUNCH_DATE=2026-01-20
```

### Step 2: Enable Maintenance Mode

To enable the maintenance page, set:
```env
MAINTENANCE_MODE_ENABLED=true
MAINTENANCE_END_DATE=2026-01-20 12:00:00
```

To disable and show the normal landing page, set:
```env
MAINTENANCE_MODE_ENABLED=false
```

### Step 3: Enable Coming Soon Mode

To enable the coming soon page, set:
```env
COMING_SOON_ENABLED=true
COMING_SOON_LAUNCH_DATE=2026-01-20
```

To disable and show the normal landing page, set:
```env
COMING_SOON_ENABLED=false
```

### Step 4: Set Dates

Update the dates as needed:
- Maintenance: `MAINTENANCE_END_DATE=2026-01-20 12:00:00` (includes time)
- Coming Soon: `COMING_SOON_LAUNCH_DATE=2026-01-20` (date only)

## How It Works

### Maintenance Mode (Priority)
1. When `MAINTENANCE_MODE_ENABLED=true`, **ALL pages** will redirect to the maintenance page
2. The middleware (`MaintenanceModeMiddleware`) intercepts all requests and redirects them to `/`
3. The root URL (`/`) displays the maintenance page when enabled
4. Shows "We'll Be Back Soon!" with maintenance message
5. Countdown shows "Estimated Time Remaining" until `MAINTENANCE_END_DATE`

### Coming Soon Mode
1. When `COMING_SOON_ENABLED=true` (and maintenance is disabled), **ALL pages** will redirect to the coming soon page
2. The middleware (`ComingSoonMiddleware`) intercepts all requests and redirects them to `/`
3. The root URL (`/`) displays the coming soon page when enabled
4. Shows "We're Launching Soon!" with launch message
5. Countdown shows "Launch Countdown" until `COMING_SOON_LAUNCH_DATE`

### General
- API routes and health check routes are excluded from redirects
- The countdown timers automatically calculate the time remaining
- When dates pass, appropriate messages are shown
- The contact email is pulled from `MAIL_FROM_ADDRESS` in your `.env` file
- **Maintenance mode takes priority** - if both are enabled, maintenance page is shown

## Features

- ✅ Animated gradient background using brand colors (#055498, #123a60)
- ✅ Floating animated circles with brand accent colors
- ✅ Rotating gear icon with glow effect
- ✅ Live countdown timer (Days, Hours, Minutes, Seconds)
- ✅ Responsive design for all devices
- ✅ Smooth animations and transitions
- ✅ Brand-consistent styling

## Files Created

- `resources/views/maintenance.blade.php` - The maintenance page view
- `resources/views/coming-soon.blade.php` - The coming soon page view
- `app/Http/Middleware/MaintenanceModeMiddleware.php` - Middleware to redirect all pages during maintenance
- `app/Http/Middleware/ComingSoonMiddleware.php` - Middleware to redirect all pages during coming soon
- `routes/web.php` - Updated to check for maintenance and coming soon modes
- `config/app.php` - Added configuration for maintenance and coming soon settings
- `bootstrap/app.php` - Registered the middleware globally

## Notes

- Both pages will show for ALL users (including logged-in users) when enabled
- **ALL pages** will redirect to the maintenance/coming soon page when enabled (except API routes)
- **Maintenance mode takes priority** - if both are enabled, maintenance page is shown
- Make sure to disable them before your actual launch/maintenance completion
- The dates can be changed anytime by updating the respective date variables in `.env`
- API routes (`/api/*`) and health check routes (`/up`, `/health`) are excluded from redirects
- Maintenance end date includes time (`YYYY-MM-DD HH:MM:SS`), coming soon date is date only (`YYYY-MM-DD`)

