# CSRF Token Mismatch - Server Fix Commands

## Run These Commands on Your AlwaysData Server

### Step 1: SSH into your server
```bash
ssh landogz@ssh-landogz.alwaysdata.net
```

### Step 2: Navigate to project
```bash
cd /home/landogz/boardsmemberportal
```

### Step 3: Pull latest changes (includes TrustProxies fix)
```bash
git pull origin main
```

### Step 4: Update .env file
```bash
nano .env
```

**Make sure these are set correctly:**
```env
APP_URL=https://landogz.alwaysdata.net
APP_ENV=production
APP_DEBUG=false

SESSION_DRIVER=file
SESSION_LIFETIME=120
SESSION_DOMAIN=null
SESSION_SECURE_COOKIE=true
SESSION_SAME_SITE=lax

# If using HTTPS (which you should)
APP_URL=https://landogz.alwaysdata.net
```

**Save and exit:** `Ctrl+O`, `Enter`, `Ctrl+X`

### Step 5: Clear ALL caches
```bash
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear
php artisan optimize:clear
```

### Step 6: Fix storage permissions
```bash
chmod -R 775 storage
chmod -R 775 bootstrap/cache
```

### Step 7: Rebuild caches
```bash
php artisan config:cache
php artisan route:cache
```

### Step 8: Test session
```bash
php artisan tinker --execute="session(['test' => 'works']); echo session('test');"
```

Should output: `works`

### Step 9: Check APP_URL
```bash
php artisan tinker --execute="echo config('app.url');"
```

Should output: `https://landogz.alwaysdata.net`

## If Still Not Working:

### Option A: Change Session Driver to File
```bash
# Edit .env
nano .env
# Change: SESSION_DRIVER=file
# Save and clear cache
php artisan config:clear
php artisan config:cache
```

### Option B: Check Session Storage
```bash
# Check if sessions directory exists and is writable
ls -la storage/framework/sessions
# If empty or doesn't exist:
mkdir -p storage/framework/sessions
chmod -R 775 storage/framework/sessions
```

### Option C: Verify TrustProxies
```bash
# Check if TrustProxies is in bootstrap/app.php
grep -n "trustProxies" bootstrap/app.php
```

Should show: `$middleware->trustProxies(at: '*');`

### Option D: Clear Browser Cookies
- Clear cookies for `landogz.alwaysdata.net`
- Or use incognito/private browsing mode
- Or try a different browser

### Option E: Check Session Configuration
```bash
php artisan tinker --execute="print_r(config('session'));"
```

Check that:
- `driver` = `file`
- `domain` = `null`
- `secure` = `true` (if using HTTPS)
- `same_site` = `lax`

## Quick One-Liner Fix (Run all at once):
```bash
cd /home/landogz/boardsmemberportal && \
git pull origin main && \
php artisan config:clear && \
php artisan cache:clear && \
php artisan route:clear && \
php artisan view:clear && \
chmod -R 775 storage bootstrap/cache && \
php artisan config:cache && \
php artisan route:cache
```

## Verify Fix:
1. Open browser: `https://landogz.alwaysdata.net/login`
2. Try to login
3. Check browser console (F12) for any errors
4. Check if CSRF token is present in page source:
   - View page source
   - Search for: `csrf-token`
   - Should see: `<meta name="csrf-token" content="...">`

## Common Issues:

1. **APP_URL still set to localhost** → Update to `https://landogz.alwaysdata.net`
2. **Session driver is database but table doesn't exist** → Change to `file` or run migrations
3. **Storage not writable** → Fix permissions with `chmod -R 775 storage`
4. **HTTPS/HTTP mismatch** → Ensure `SESSION_SECURE_COOKIE=true` for HTTPS
5. **Cached old config** → Clear all caches

