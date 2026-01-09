# Laravel Reverb SIGINT Error Fix

## Problem
```
Undefined constant "Laravel\Reverb\Servers\Reverb\Console\Commands\SIGINT"
```

This error occurs because the `pcntl` PHP extension is not installed or enabled on your server.

## Solution

### Step 1: Check if pcntl is installed

On your server, run:
```bash
php -m | grep pcntl
```

If it returns nothing, `pcntl` is not installed.

### Step 2: Check PHP version and extensions

```bash
php -v
php -m
```

### Step 3: Install pcntl extension (if not installed)

**For AlwaysData:**
AlwaysData may not allow installing PHP extensions directly. You need to:

1. **Check AlwaysData PHP configuration:**
   - Log in to AlwaysData admin panel
   - Go to **Advanced > PHP**
   - Check available PHP versions and extensions

2. **Contact AlwaysData Support:**
   - Ask them to enable the `pcntl` extension for your account
   - Or ask if it's available in a different PHP version

3. **Alternative: Use a different PHP version:**
   ```bash
   # Check available PHP versions
   php -v
   
   # AlwaysData might have multiple PHP versions
   # Try switching to a version that has pcntl
   ```

### Step 4: Temporary Workaround (If pcntl cannot be installed)

If `pcntl` cannot be installed, you have a few options:

#### Option A: Disable Reverb (if not critical)
If you don't need real-time WebSocket features immediately:

1. Change broadcast driver in `.env`:
   ```env
   BROADCAST_DRIVER=log
   # or
   BROADCAST_DRIVER=null
   ```

2. Comment out Reverb routes if any

#### Option B: Use Alternative WebSocket Solution
- Use Pusher or other WebSocket services
- Or use Laravel's queue-based broadcasting

#### Option C: Patch Reverb (Not Recommended)
You could patch the vendor file, but this is not recommended as it will be overwritten on updates.

### Step 5: Verify pcntl Installation

Once pcntl is installed, verify:
```bash
php -r "echo extension_loaded('pcntl') ? 'pcntl is loaded' : 'pcntl is NOT loaded';"
```

Should output: `pcntl is loaded`

### Step 6: Test Reverb Again

After pcntl is installed:
```bash
php artisan reverb:start
```

## AlwaysData Specific Notes

AlwaysData shared hosting may have restrictions on:
- Installing PHP extensions
- Running long-lived processes
- WebSocket servers

**Recommendations:**
1. Contact AlwaysData support to enable `pcntl`
2. Consider upgrading to a VPS if you need full control
3. Use alternative broadcasting methods if Reverb is not critical

## Alternative: Use Pusher or Other Services

If Reverb cannot work on AlwaysData, consider:

1. **Pusher** (commercial WebSocket service)
   ```bash
   composer require pusher/pusher-php-server
   ```

2. **Laravel Echo Server** (requires Node.js)

3. **Queue-based broadcasting** (no WebSocket needed)

## Check Current Status

Run these commands to diagnose:

```bash
# Check PHP version
php -v

# Check if pcntl is loaded
php -r "var_dump(extension_loaded('pcntl'));"

# Check available functions
php -r "var_dump(function_exists('pcntl_signal'));"

# Check if constants are defined
php -r "var_dump(defined('SIGINT'));"
```

If `pcntl` is loaded, all should return `true`.

