# How to Update PHP Upload Limits in XAMPP

If you're getting "POST data is too large" errors when uploading files, you need to update your PHP configuration.

## ⚠️ IMPORTANT: Check Which php.ini Your Server is Using

**Different PHP servers use different configuration files!**

1. **Check your PHP configuration:**
   - **Option 1 (Recommended):** Visit: `http://localhost:8000/phpinfo` (Laravel route - requires login as admin)
   - **Option 2:** Visit: `http://localhost/boardsmemberportal/public/phpinfo.php` (if using XAMPP Apache)
   - Search for "Loaded Configuration File" - this shows which php.ini is being used
   - Search for "Server API" - shows if you're using PHP built-in server or Apache
   - Look for "upload_max_filesize" and "post_max_size" values

2. **Edit the CORRECT php.ini file** (the one shown in phpinfo)

### Common Scenarios:

- **PHP Built-in Server** (`php artisan serve`): Uses Homebrew PHP at `/usr/local/etc/php/8.3/php.ini`
- **XAMPP Apache**: Uses XAMPP PHP at `/Applications/XAMPP/xamppfiles/etc/php.ini`

## Quick Fix Based on Your Server Type:

### If Using PHP Built-in Server (`php artisan serve`):

**Your phpinfo shows you're using PHP Built-in Server with Homebrew PHP!**

1. **Edit the Homebrew PHP configuration:**
   ```bash
   sudo nano /usr/local/etc/php/8.3/php.ini
   ```

2. **Search for (Press Cmd+W or Ctrl+W):**
   - `upload_max_filesize = 2M` → Change to `upload_max_filesize = 30M`
   - `post_max_size = 8M` → Change to `post_max_size = 30M`
   - `max_execution_time = 30` → Change to `max_execution_time = 300`
   - `max_input_time = 60` → Change to `max_input_time = 300`

3. **Save:** Press `Ctrl+O`, then `Enter`, then `Ctrl+X` to exit

4. **Restart PHP development server:**
   - Stop the current server (Ctrl+C in terminal)
   - Start again: `php artisan serve`

### If Using XAMPP Apache:

### Method 1: Using XAMPP Control Panel (RECOMMENDED)

1. **Open XAMPP Control Panel**
2. **Click "Config" button next to Apache**
3. **Select "PHP (php.ini)"** - This opens the correct php.ini file
4. **Press Cmd+F (or Ctrl+F) to search for:**
   - `upload_max_filesize` → Change value to `30M`
   - `post_max_size` → Change value to `30M`
   - `max_execution_time` → Change value to `300`
   - `max_input_time` → Change value to `300`
   - `memory_limit` → Change value to `256M` (optional but recommended)
5. **Save the file (Cmd+S or Ctrl+S)**
6. **Restart Apache:**
   - In XAMPP Control Panel, click "Stop" for Apache
   - Wait a few seconds
   - Click "Start" for Apache

### Method 2: Using Terminal

1. **Find the correct php.ini location:**
   ```bash
   # Check which php.ini Apache uses
   # Visit http://localhost/boardsmemberportal/phpinfo.php and look for "Loaded Configuration File"
   ```

2. **Edit the php.ini file:**
   ```bash
   # Usually one of these locations:
   sudo nano /Applications/XAMPP/xamppfiles/etc/php.ini
   # OR
   sudo nano /Applications/XAMPP/xamppfiles/etc/php.ini-development
   # OR
   sudo nano /Applications/XAMPP/xamppfiles/etc/php.ini-production
   ```

3. **Search and update (Press Ctrl+W to search):**
   - `upload_max_filesize = 2M` → Change to `upload_max_filesize = 30M`
   - `post_max_size = 8M` → Change to `post_max_size = 30M`
   - `max_execution_time = 30` → Change to `max_execution_time = 300`
   - `max_input_time = 60` → Change to `max_input_time = 300`

4. **Save:** Press `Ctrl+O`, then `Enter`, then `Ctrl+X` to exit

5. **Restart Apache in XAMPP Control Panel**

## Alternative Method (XAMPP Control Panel):

1. **Open XAMPP Control Panel**
2. **Click "Config" button next to Apache**
3. **Select "PHP (php.ini)"**
4. **Search for and update:**
   - `upload_max_filesize = 30M`
   - `post_max_size = 30M`
   - `max_execution_time = 300`
   - `max_input_time = 300`
5. **Save the file**
6. **Restart Apache** in XAMPP Control Panel

## Verify Changes

**IMPORTANT:** CLI PHP and Apache PHP can show different values!

### Verify Apache PHP Configuration:
1. Visit: `http://localhost/boardsmemberportal/phpinfo.php`
2. Search for "upload_max_filesize" - should show `30M`
3. Search for "post_max_size" - should show `30M`

### Verify CLI PHP (optional):
```bash
php -i | grep -E "upload_max_filesize|post_max_size"
```

**Note:** CLI values may differ from Apache values. What matters is the Apache values shown in phpinfo.php.

## Troubleshooting

### If changes don't take effect:

1. **Make sure you edited the CORRECT php.ini file**
   - Check phpinfo.php to see which file Apache is using
   - XAMPP might use `php.ini-development` or `php.ini-production`

2. **Restart Apache completely:**
   - Stop Apache in XAMPP Control Panel
   - Wait 5 seconds
   - Start Apache again

3. **Check for multiple PHP installations:**
   - XAMPP has its own PHP
   - System PHP might be different
   - Make sure you're editing XAMPP's php.ini

4. **Clear browser cache** and try uploading again

## Security Note

**DELETE `phpinfo.php` after checking!** It exposes sensitive server information.

