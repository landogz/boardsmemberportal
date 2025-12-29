# Cron Job Setup for Laravel Scheduler

## Why You Need This

The Laravel scheduler (`php artisan schedule:run`) **does not run automatically**. It must be triggered by a cron job on your server. Without it:
- ❌ Scheduled announcements will NOT be automatically published
- ❌ Idle user check will NOT run automatically
- ❌ Any scheduled tasks will NOT execute

## Setup Instructions

### For Production Server (Domain)

#### Step 1: SSH into your server
```bash
ssh your-username@your-domain.com
```

#### Step 2: Open crontab
```bash
crontab -e
```

#### Step 3: Add this line (replace with your actual project path)
```bash
* * * * * cd /path/to/your/project && php artisan schedule:run >> /dev/null 2>&1
```

**Example for typical hosting:**
```bash
* * * * * cd /home/username/public_html && php artisan schedule:run >> /dev/null 2>&1
```

**Or if you need full PHP path:**
```bash
* * * * * cd /home/username/public_html && /usr/bin/php artisan schedule:run >> /dev/null 2>&1
```

#### Step 4: Find your PHP path (if needed)
```bash
which php
# or
whereis php
```

#### Step 5: Save and exit
- **nano**: `Ctrl+X`, then `Y`, then `Enter`
- **vi**: `Esc`, type `:wq`, then `Enter`

#### Step 6: Verify it's added
```bash
crontab -l
```

### For cPanel Hosting

1. Log into cPanel
2. Go to **Cron Jobs** (under Advanced section)
3. Add a new cron job:
   - **Minute**: `*`
   - **Hour**: `*`
   - **Day**: `*`
   - **Month**: `*`
   - **Weekday**: `*`
   - **Command**: 
     ```bash
     cd /home/username/public_html && php artisan schedule:run >> /dev/null 2>&1
     ```
   - Replace `/home/username/public_html` with your actual project path

### For Shared Hosting (without SSH access)

If you don't have SSH access, you may need to:
1. Contact your hosting provider to set up the cron job
2. Or use a web-based cron service that pings a URL (less reliable)
3. Or upgrade to a hosting plan with SSH/cron access

### Testing the Setup

#### Test the scheduler manually:
```bash
cd /path/to/your/project
php artisan schedule:run
```

#### Test the specific command:
```bash
php artisan announcements:publish-scheduled
```

#### Check scheduled tasks:
```bash
php artisan schedule:list
```

## What Gets Scheduled

Currently, these tasks run every minute:
1. **`users:check-idle`** - Checks for idle users and logs them out after 30 minutes
2. **`announcements:publish-scheduled`** - Publishes scheduled announcements automatically

## Troubleshooting

### Cron job not running?
1. Check if cron service is running: `systemctl status cron` (Linux) or check your hosting control panel
2. Verify the path is correct: `cd /path/to/project && pwd`
3. Check PHP path: `which php` or `php -v`
4. Check Laravel logs: `storage/logs/laravel.log`
5. Test manually: `php artisan schedule:run -v` (verbose mode)

### Permission issues?
- Make sure the cron user has read/write access to the project directory
- Check file permissions: `ls -la`

### Not receiving expected results?
- Check if the command is registered: `php artisan list | grep publish`
- Check Laravel logs for errors
- Verify database connection is working

## Important Notes

- The cron job runs as the user who owns the crontab (usually your server user)
- Make sure that user has proper permissions to access the Laravel application
- The `>> /dev/null 2>&1` part suppresses output (remove it if you want to see logs)
- For better logging, you can redirect to a file:
  ```bash
  * * * * * cd /path/to/project && php artisan schedule:run >> /path/to/project/storage/logs/scheduler.log 2>&1
  ```

## Alternative: Web-based Cron (Not Recommended)

If you absolutely cannot set up a server cron job, you can create a route that triggers the scheduler and call it from an external cron service:

```php
// routes/web.php
Route::get('/cron/run', function() {
    Artisan::call('schedule:run');
    return 'Scheduler executed';
})->middleware('auth'); // Add authentication for security
```

Then use a service like:
- https://cron-job.org
- https://www.easycron.com

**Note:** This is less reliable and less secure than a proper server cron job.

