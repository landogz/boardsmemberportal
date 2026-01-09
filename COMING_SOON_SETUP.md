# Coming Soon Page Setup

## Overview
The coming soon page is a beautiful, animated landing page that displays when the website is not yet launched. It features a countdown timer and follows the Board Member Portal brand colors.

## Configuration

### Step 1: Add to .env file

Add these lines to your `.env` file:

```env
# Coming Soon Mode
# Set to true to show the coming soon page, false to show normal landing page
COMING_SOON_ENABLED=false

# Launch date for the countdown timer (format: YYYY-MM-DD)
COMING_SOON_LAUNCH_DATE=2026-01-20
```

### Step 2: Enable Coming Soon Mode

To enable the coming soon page, set:
```env
COMING_SOON_ENABLED=true
```

To disable and show the normal landing page, set:
```env
COMING_SOON_ENABLED=false
```

### Step 3: Set Launch Date

Update the launch date in the format `YYYY-MM-DD`:
```env
COMING_SOON_LAUNCH_DATE=2026-01-20
```

## How It Works

1. When `COMING_SOON_ENABLED=true`, all visitors to the root URL (`/`) will see the coming soon page
2. The countdown timer automatically calculates the time remaining until the launch date
3. When the launch date passes, the countdown shows "We're live! Welcome to Board Member Portal!"
4. The contact email is pulled from `MAIL_FROM_ADDRESS` in your `.env` file

## Features

- ✅ Animated gradient background using brand colors (#055498, #123a60)
- ✅ Floating animated circles with brand accent colors
- ✅ Rotating gear icon with glow effect
- ✅ Live countdown timer (Days, Hours, Minutes, Seconds)
- ✅ Responsive design for all devices
- ✅ Smooth animations and transitions
- ✅ Brand-consistent styling

## Files Created

- `resources/views/coming-soon.blade.php` - The coming soon page view
- `routes/web.php` - Updated to check for coming soon mode
- `config/app.php` - Added configuration for coming soon settings

## Notes

- The coming soon page will show for ALL users (including logged-in users) when enabled
- Make sure to disable it before your actual launch date
- The launch date can be changed anytime by updating `COMING_SOON_LAUNCH_DATE` in `.env`

