# Laravel Application Deployment Instructions

Complete guide for deploying the Board Member Portal to a production server.

**Configuration (replace with your values):**
- Domain: `your-domain.com`
- Project path: `/var/www/boardsmemberportal`  
  - Web root (served by Nginx/Apache): `/var/www/boardsmemberportal/public`

## Prerequisites

- SSH access to your server
- PHP 8.2+
- Composer
- MySQL or MariaDB
- Node.js and npm (for building assets)
- Git

---

## Step 1: Connect to Your Server via SSH

You need terminal access to the server to run deployment commands. Use **SSH (Secure Shell)** to connect.

### What you need

- **Server address**: IP address (e.g. `116.50.254.36`) or hostname (e.g. `your-domain.com`)
- **Username**: the account name on the server (e.g. `bmpap`, `root`, or a user created by your host)
- **Password or SSH key**: from your hosting provider or server admin

### Connect from Mac or Linux

1. Open **Terminal**.
2. Run (replace with your username and server address):

```bash
ssh your-username@your-server-address
```

Example:

```bash
ssh bmpap@116.50.254.36
```

3. When prompted, enter the password. You will not see characters as you type (no dots or asterisks); that is normal.
4. On first connection you may see a message about the host key; type `yes` and press Enter.

If the server uses a **non-default SSH port** (e.g. 2222):

```bash
ssh -p 2222 your-username@your-server-address
```

### Connect from Windows

**Option A – Windows 10/11 (PowerShell or Windows Terminal)**

1. Open **PowerShell** or **Windows Terminal** (search in Start menu).
2. Run the same command as above:

```bash
ssh your-username@your-server-address
```

3. Enter the password when prompted.

**Option B – PuTTY**

1. Download PuTTY from https://www.putty.org/ and open it.
2. **Host Name**: enter the IP or hostname (e.g. `116.50.254.36`).
3. **Port**: `22` (or the port given by your host). Click **Open**.
4. When the terminal opens, enter your username and then your password when prompted.

### After you are connected

You should see a welcome message and a shell prompt (e.g. `bmpap@hostname:~$`). All following deployment steps assume you run commands in this SSH session (or in new SSH sessions to the same server).

---

## Step 2: Install Prerequisites (Ubuntu/Debian)

> These are quick examples for a fresh Ubuntu/Debian server. For other Linux distributions (CentOS, Rocky, Alma, etc.), use the equivalent `yum`/`dnf` commands or follow the official docs linked below.

### PHP 8.2+

- Official docs: https://www.php.net/manual/en/install.php

```bash
sudo apt update

# Install required extensions for Laravel (tune as needed)
sudo apt install -y \
  php8.2 php8.2-cli php8.2-fpm \
  php8.2-mbstring php8.2-xml php8.2-curl php8.2-zip php8.2-mysql \
  php8.2-intl php8.2-gd

php -v   # should show PHP 8.2.x
```

### Composer

- Official docs: https://getcomposer.org/download/

```bash
cd ~
php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
php composer-setup.php --install-dir=/usr/local/bin --filename=composer
rm composer-setup.php

composer --version
```

### MySQL or MariaDB

- MySQL docs: https://dev.mysql.com/doc/
- MariaDB docs: https://mariadb.com/kb/en/documentation/

```bash
sudo apt update

# Install MySQL Server (swap with mariadb-server if you prefer MariaDB)
sudo apt install -y mysql-server

# Secure installation (set root password, remove test DB, etc.)
sudo mysql_secure_installation
```

### Node.js and npm (for building assets)

- Node.js downloads: https://nodejs.org/en/download
- Recommended: install via NodeSource or nvm for a current LTS version.

**Option 1 – NodeSource (example for Node 20 LTS):**

```bash
curl -fsSL https://deb.nodesource.com/setup_20.x | sudo -E bash -
sudo apt install -y nodejs

node -v
npm -v
```

**Option 2 – nvm (Node Version Manager):**

```bash
curl -o- https://raw.githubusercontent.com/nvm-sh/nvm/v0.39.7/install.sh | bash
source ~/.bashrc
nvm install --lts

node -v
npm -v
```

### Git

- Git docs: https://git-scm.com/book/en/v2/Getting-Started-Installing-Git

```bash
sudo apt update
sudo apt install -y git

git --version
```

---

## Step 3: Clone the Repository

```bash
# Navigate to your web root (e.g. /var/www or your host’s document root)
cd /var/www

# Clone the repository
git clone https://github.com/landogz/boardsmemberportal.git

cd boardsmemberportal
```

---

## Step 4: Install PHP Dependencies

```bash
# Install Composer dependencies
composer install --optimize-autoloader --no-dev

# If composer is not installed globally, download it first:
# curl -sS https://getcomposer.org/installer | php
# php composer.phar install --optimize-autoloader --no-dev
```

---

## Step 5: Environment Configuration

```bash
# Copy the environment file
cp .env.example .env

# Generate application key
php artisan key:generate

# Edit the .env file with your configuration
nano .env
# or
vi .env
```

### Required .env Configuration (Template):

```env
APP_NAME="Board Member Portal"
APP_ENV=production
APP_KEY=base64:... (generated by key:generate)
APP_DEBUG=false
APP_URL=https://your-domain.com

LOG_CHANNEL=stack
LOG_LEVEL=error

DB_CONNECTION=mysql
DB_HOST=your-db-host
DB_PORT=3306
DB_DATABASE=your_db_name
DB_USERNAME=your_db_user
DB_PASSWORD=your_db_password

# Single recipient for Contact Us form (landing page)
CONTACT_RECIPIENT_EMAIL=boardsec@example.com
```

---

## Step 6: Database Setup

```bash
# Create database (if not already created via cPanel/phpMyAdmin)
# You may need to do this via your hosting control panel

# Run migrations
php artisan migrate --force

# Seed the database (optional, for initial setup)
php artisan db:seed --class=RolePermissionSeeder
php artisan db:seed --class=GovernmentAgencySeeder
```

---

## Step 7: Storage and Cache Setup

```bash
# Create symbolic link for storage
php artisan storage:link

# Clear and cache configuration
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Clear application cache
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
```

---

## Step 8: Install and Build Frontend Assets

```bash
# Install Node.js dependencies
npm install

# Build production assets
npm run build

# Or if you need to watch for changes during development:
# npm run dev
```

---

## Step 9: Set Permissions

```bash
# From project root; use your web server user (e.g. www-data, apache, nginx)
chmod -R 775 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache
```

---

## Step 10: Configure Web Server

> This step is usually handled by the client’s hosting / infrastructure team.  
> The only requirement is that the web server’s **document root** points to the Laravel `public` directory:
>
> - Project path: `/var/www/boardsmemberportal`  
> - Web root (document root): `/var/www/boardsmemberportal/public`

---

## Step 11: Setup Laravel Reverb (WebSocket Server)

### Install Reverb

```bash
# Reverb should already be in composer.json, but if not:
composer require laravel/reverb

# Publish Reverb configuration
php artisan reverb:install
```

### Run Reverb

Use a process manager so Reverb keeps running (e.g. Supervisor or your host’s process manager).

**Option 1: Supervisor**

```bash
sudo nano /etc/supervisor/conf.d/reverb.conf
```

Add (adjust paths to your project):

```ini
[program:reverb]
process_name=%(program_name)s_%(process_num)02d
command=php /var/www/boardsmemberportal/artisan reverb:start --host=0.0.0.0 --port=8080
autostart=true
autorestart=true
stopasgroup=true
killasgroup=true
user=www-data
numprocs=1
redirect_stderr=true
stdout_logfile=/var/www/boardsmemberportal/storage/logs/reverb.log
stopwaitsecs=3600
```

Then: `sudo supervisorctl reread && sudo supervisorctl update && sudo supervisorctl start reverb`

**Option 2: screen/tmux (temporary)**

```bash
screen -S reverb
cd /var/www/boardsmemberportal
php artisan reverb:start --host=0.0.0.0 --port=8080
# Detach: Ctrl+A then D. Reattach: screen -r reverb
```

---

## Step 12: Setup Queue Workers

Run a queue worker so jobs (e.g. emails) are processed. Use Supervisor or your host’s process manager.

```bash
sudo nano /etc/supervisor/conf.d/laravel-worker.conf
```

Add (adjust paths):

```ini
[program:laravel-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /var/www/boardsmemberportal/artisan queue:work --sleep=3 --tries=3 --max-time=3600
autostart=true
autorestart=true
stopasgroup=true
killasgroup=true
user=www-data
numprocs=1
redirect_stderr=true
stdout_logfile=/var/www/boardsmemberportal/storage/logs/worker.log
stopwaitsecs=3600
```

Then: `sudo supervisorctl reread && sudo supervisorctl update && sudo supervisorctl start laravel-worker`

---

## Step 13: Setup Task Scheduler (Cron)

Run the scheduler every minute:

```bash
crontab -e
```

Add (adjust path):

```cron
* * * * * cd /var/www/boardsmemberportal && php artisan schedule:run >> /dev/null 2>&1
```

---

## Step 14: SSL Certificate (HTTPS)

Enable HTTPS (e.g. Let’s Encrypt via Certbot or your host’s control panel). Then set in `.env`:

```env
APP_URL=https://your-domain.com
REVERB_SCHEME=https
```

---

## Step 15: Final Verification

```bash
# Check application status
php artisan about

# Test database connection
php artisan tinker
# Then run: DB::connection()->getPdo();

# Check queue status
php artisan queue:work --once

# Check scheduled tasks
php artisan schedule:list

# View logs
tail -f storage/logs/laravel.log
```

---

## Step 16: Update Application (Future Deployments)

```bash
cd /var/www/boardsmemberportal

git pull origin main

composer install --optimize-autoloader --no-dev
npm install
npm run build

php artisan migrate --force

php artisan config:cache
php artisan route:cache
php artisan view:cache

# Restart Reverb and queue worker (e.g. sudo supervisorctl restart reverb laravel-worker)
```

---

## Troubleshooting

### Permission Issues

```bash
# Ensure web server can write to storage and bootstrap/cache
chmod -R 775 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache
# (Replace www-data with your web server user if different)
```

### Clear All Caches

```bash
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan optimize:clear
```

### Check Logs

```bash
# Application logs
tail -f storage/logs/laravel.log

# Reverb and queue worker logs
tail -f storage/logs/reverb.log
tail -f storage/logs/worker.log
```

### Database Connection Issues

```bash
# Test database connection
php artisan tinker
# Then: DB::connection()->getPdo();

# Check .env file
cat .env | grep DB_
```

### Reverb Connection Issues

- Ensure port 8080 is open in firewall
- Check Reverb service is running: `sudo systemctl status reverb`
- Verify REVERB_* variables in .env
- Check browser console for WebSocket connection errors

---

## Security Checklist

- [ ] Set `APP_DEBUG=false` in production
- [ ] Use strong database passwords
- [ ] Enable HTTPS/SSL
- [ ] Set proper file permissions
- [ ] Keep dependencies updated
- [ ] Use environment variables for sensitive data
- [ ] Enable firewall
- [ ] Regular backups of database and files
- [ ] Keep Laravel and packages updated

---

## Backup Strategy

### Database Backup

**Via command line**

```bash
# Create backup directory
mkdir -p ~/backups

# Create backup script
nano ~/backups/backup-db.sh
```

Add (replace with your actual database credentials):

```bash
#!/bin/bash
DATE=$(date +%Y%m%d_%H%M%S)
mysqldump -u your_db_user -p'your_db_password' your_database_name > ~/backups/db_backup_$DATE.sql
# Keep only last 7 days
find ~/backups -name "db_backup_*.sql" -mtime +7 -delete
```

Make executable and schedule:

```bash
chmod +x ~/backups/backup-db.sh
crontab -e
# Add: 0 2 * * * ~/backups/backup-db.sh
```

### File Backup

```bash
# Create backup directory
mkdir -p ~/backups

# Backup storage and important files
tar -czf ~/backups/files_backup_$(date +%Y%m%d).tar.gz /var/www/boardsmemberportal/storage

# Keep only last 7 days
find ~/backups -name "files_backup_*.tar.gz" -mtime +7 -delete
```

---

## Support

For issues or questions:
- Check Laravel documentation: https://laravel.com/docs
- Check Laravel Reverb documentation: https://laravel.com/docs/reverb
- Review application logs in `storage/logs/`

---

Replace `/var/www/boardsmemberportal` with your actual project path wherever it appears in these instructions.
