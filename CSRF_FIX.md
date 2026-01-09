# CSRF Token Mismatch - Fix Guide

## Quick Fixes (Run on Server)

### 1. Clear All Caches
```bash
cd /home/landogz/boardsmemberportal
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear
php artisan optimize:clear
```

### 2. Update .env File

Edit your `.env` file and ensure these settings:

```env
APP_URL=https://landogz.alwaysdata.net
APP_ENV=production
APP_DEBUG=false

SESSION_DRIVER=file
SESSION_LIFETIME=120
SESSION_DOMAIN=null

# For HTTPS sites
SESSION_SECURE_COOKIE=true
SESSION_SAME_SITE=lax
```

### 3. Check Session Storage Permissions

```bash
# Ensure storage directory is writable
chmod -R 775 /home/landogz/boardsmemberportal/storage
chmod -R 775 /home/landogz/boardsmemberportal/bootstrap/cache
```

### 4. Rebuild Config Cache

```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### 5. Check TrustProxies Configuration

In Laravel 11, create or update `bootstrap/app.php`:

```php
->withMiddleware(function (Middleware $middleware) {
    $middleware->trustProxies(at: '*');
    // or for AlwaysData specifically:
    // $middleware->trustProxies(at: ['127.0.0.1', '::1']);
})
```

Or create `app/Http/Middleware/TrustProxies.php`:

```php
<?php

namespace App\Http\Middleware;

use Illuminate\Http\Middleware\TrustProxies as Middleware;
use Illuminate\Http\Request;

class TrustProxies extends Middleware
{
    /**
     * The trusted proxies for this application.
     *
     * @var array<int, string>|string|null
     */
    protected $proxies = '*';

    /**
     * The headers that should be used to detect proxies.
     *
     * @var int
     */
    protected $headers =
        Request::HEADER_X_FORWARDED_FOR |
        Request::HEADER_X_FORWARDED_HOST |
        Request::HEADER_X_FORWARDED_PORT |
        Request::HEADER_X_FORWARDED_PROTO |
        Request::HEADER_X_FORWARDED_AWS_ELB;
}
```

Then register it in `bootstrap/app.php`:

```php
->withMiddleware(function (Middleware $middleware) {
    $middleware->append(\App\Http\Middleware\TrustProxies::class);
})
```

## Common Causes:

1. **APP_URL Mismatch** - Must match your actual domain
2. **Session Domain** - Should be `null` or match your domain
3. **HTTPS/HTTP Mismatch** - Ensure SESSION_SECURE_COOKIE matches your scheme
4. **Cache Issues** - Old cached config with wrong settings
5. **Storage Permissions** - Can't write session files
6. **Proxy Issues** - AlwaysData uses proxies, need TrustProxies middleware

## Debug Steps:

1. Check current APP_URL:
```bash
php artisan tinker --execute="echo config('app.url');"
```

2. Check session configuration:
```bash
php artisan tinker --execute="print_r(config('session'));"
```

3. Test session write:
```bash
php artisan tinker --execute="session(['test' => 'value']); echo session('test');"
```

4. Check storage permissions:
```bash
ls -la storage/framework/sessions
```

## AlwaysData Specific:

AlwaysData uses reverse proxies. You MUST:
1. Set `APP_URL` correctly
2. Configure TrustProxies middleware
3. Ensure HTTPS is properly configured if using SSL

