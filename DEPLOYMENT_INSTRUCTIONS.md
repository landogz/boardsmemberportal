# Laravel Application Deployment Instructions

Complete guide for deploying the Board Member Portal to a production server with a **separate database server**.

> **Replace the example IPs:** Use **your actual public IP** (or domain name) for the app server everywhere `116.50.254.36` appears (`APP_URL`, Nginx `server_name`, SSH, browser). Use **your real database host** (private LAN IP, VPN address, or hostname your app can reach) instead of `192.168.1.100`.

---

## Configuration

### App Server

* Public IP or domain: `116.50.254.36` *(change to your actual public IP or hostname)*
* Project path: `/var/www/boardsmemberportal`
* Web root: `/var/www/boardsmemberportal/public`
* SSH User: `bmpap`

### Database Server (Separate)

* DB Host: `192.168.1.100` *(change to your database server’s real IP or hostname)*
* DB Name: `boardsmemberportal`
* DB User: `bmp_user`
* DB Password: `StrongPassword123`

---

# 🖥️ PART 1 — DATABASE SERVER SETUP (FROM SCRATCH)

👉 Run these steps on your **database server** (replace `192.168.1.100` with your DB host)

---

## Step DB1: Install MySQL

```bash
sudo apt update
sudo apt install -y mysql-server
```

---

## Step DB2: Secure MySQL

```bash
sudo mysql_secure_installation
```

Recommended answers:

* Set root password → YES
* Remove anonymous users → YES
* Disallow root remote login → YES
* Remove test database → YES
* Reload privilege tables → YES

---

## Step DB3: Allow Remote Connections

```bash
sudo nano /etc/mysql/mysql.conf.d/mysqld.cnf
```

Find:

```ini
bind-address = 127.0.0.1
```

Change to:

```ini
bind-address = 0.0.0.0
```

---

## Step DB4: Restart MySQL

```bash
sudo systemctl restart mysql
```

---

## Step DB5: Create Database and User

```bash
sudo mysql -u root -p
```

Then run:

```sql
CREATE DATABASE boardsmemberportal;

CREATE USER 'bmp_user'@'%' IDENTIFIED BY 'StrongPassword123';

GRANT ALL PRIVILEGES ON boardsmemberportal.* TO 'bmp_user'@'%';

FLUSH PRIVILEGES;
EXIT;
```

---

## Step DB6: Open Firewall

```bash
sudo ufw allow 3306
```

---

## Step DB7: Test Remote Access (FROM APP SERVER)

```bash
mysql -h 192.168.1.100 -u bmp_user -p   # use your DB host instead of 192.168.1.100
```

👉 If this works ✅ your DB is ready

---

# 🖥️ PART 2 — APPLICATION SERVER SETUP

👉 Run these on your **app server** (replace `116.50.254.36` with your public IP or hostname)

---

## Step 1: Connect

```bash
ssh bmpap@116.50.254.36   # use your actual public IP or hostname
```

---

## Step 2: Install Core Packages

```bash
sudo apt update && sudo apt upgrade -y
sudo apt install -y nginx git curl unzip software-properties-common
```

---

## Step 3: Install PHP

```bash
sudo apt install -y \
php8.2 php8.2-cli php8.2-fpm \
php8.2-mbstring php8.2-xml php8.2-curl php8.2-zip php8.2-mysql \
php8.2-intl php8.2-gd
```

---

## Step 4: Install Composer

```bash
cd ~
curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer
```

---

## Step 5: Install Node.js

```bash
curl -fsSL https://deb.nodesource.com/setup_lts.x | sudo -E bash -
sudo apt install -y nodejs
```

---

## Step 6: Clone Project

```bash
cd /var/www
sudo git clone https://github.com/landogz/boardsmemberportal.git
cd boardsmemberportal
```

---

## Step 7: Install Dependencies

```bash
composer install --no-dev --optimize-autoloader
```

---

## Step 8: Setup Environment

```bash
cp .env.example .env
php artisan key:generate
nano .env
```

### FINAL `.env`

```env
APP_NAME="Board Member Portal"
APP_ENV=production
APP_KEY=
APP_DEBUG=false
APP_URL=http://116.50.254.36   # http(s)://YOUR_ACTUAL_PUBLIC_IP_OR_DOMAIN

DB_CONNECTION=mysql
DB_HOST=192.168.1.100   # YOUR_DB_HOST (reachable from the app server)
DB_PORT=3306
DB_DATABASE=boardsmemberportal
DB_USERNAME=bmp_user
DB_PASSWORD=StrongPassword123

# --- Mail (SMTP) — password resets, system mail, Contact Us, etc. ---
MAIL_MAILER=smtp
MAIL_HOST=smtp.example.com
MAIL_PORT=587
MAIL_USERNAME=your-smtp-username
MAIL_PASSWORD=your-smtp-password-or-app-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@yourdomain.com
MAIL_FROM_NAME="Board Member Portal"

# Contact Us (landing page): all submissions go to this single address
CONTACT_RECIPIENT_EMAIL=office@yourdomain.com
```

#### Email setup notes

* **SMTP:** Set `MAIL_HOST`, `MAIL_PORT`, `MAIL_USERNAME`, and `MAIL_PASSWORD` from your mail provider (office 365, Google Workspace, cPanel SMTP, transactional providers, etc.). Typical ports: **587** with `MAIL_ENCRYPTION=tls`, or **465** with `MAIL_ENCRYPTION=ssl` (follow your provider’s docs).
* **From address:** `MAIL_FROM_ADDRESS` should be an address your provider allows you to send as (often the same mailbox as `MAIL_USERNAME`).
* **Gmail:** Enable 2-Step Verification and create an **App Password**; use that as `MAIL_PASSWORD` (not your normal Gmail password). `MAIL_HOST=smtp.gmail.com`, `MAIL_PORT=587`, `MAIL_ENCRYPTION=tls`.
* **Firewall:** Allow outbound connections from the app server to your SMTP host on the chosen port.
* **Contact Us:** `CONTACT_RECIPIENT_EMAIL` must be **one valid email** (no comma-separated list). It is where Contact form messages are delivered.

> **Note:** After `php artisan key:generate`, `APP_KEY` is already set in `.env`. When you edit with `nano`, keep that line; add or adjust the other variables above.

---

## Step 9: Run Migration + Seeder

```bash
php artisan config:clear
php artisan cache:clear

php artisan migrate --seed --force
```

---

## Step 10: Frontend Build

```bash
npm install
npm run build
```

---

## Step 11: Permissions

```bash
sudo chown -R www-data:www-data /var/www/boardsmemberportal
sudo chmod -R 775 storage bootstrap/cache
```

---

## Step 12: Nginx Config

```bash
sudo nano /etc/nginx/sites-available/boardsmemberportal
```

```nginx
server {
    listen 80;
    server_name 116.50.254.36;   # your actual public IP or domain

    root /var/www/boardsmemberportal/public;
    index index.php;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        include snippets/fastcgi-php.conf;
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
    }
}
```

```bash
sudo ln -s /etc/nginx/sites-available/boardsmemberportal /etc/nginx/sites-enabled/
sudo nginx -t
sudo systemctl restart nginx
```

---

## Step 13: Cron (Laravel Scheduler)

The app uses Laravel’s task scheduler (`routes/console.php`) for idle checks, publishing scheduled announcements, unread message reminders, and daily birthday greetings. **One cron entry** must call `schedule:run` every minute.

As the user that can read the project and run PHP (often `bmpap` or `www-data`), open crontab:

```bash
crontab -e
```

Add this line (adjust the path if your project is not `/var/www/boardsmemberportal`):

```cron
* * * * * cd /var/www/boardsmemberportal && /usr/bin/php artisan schedule:run >> /dev/null 2>&1
```

If `php` is not at `/usr/bin/php`, run `which php` and use that path.

To install the same line for `www-data` instead:

```bash
sudo crontab -u www-data -e
```

Then paste the same `* * * * *` line.

Verify the scheduler is registered:

```bash
cd /var/www/boardsmemberportal && php artisan schedule:list
```

---

## Step 14: Reset portal data (optional — destructive)

The app includes an Artisan command to **truncate** core portal content (announcements, notices, referendums, attendance, agenda requests, reference materials, banner slides, messaging tables, etc.). It is intended for **staging resets**, **demos**, or **controlled maintenance**, not routine production use.

> **Warning:** This **permanently deletes** data. Take a **database backup** first. Do **not** run on production without explicit approval.

From the project directory:

```bash
cd /var/www/boardsmemberportal
php artisan portal:reset-data
```

The command asks for confirmation before running.

**Also remove Board Member and CONSEC users** (keeps users whose `privilege` is not `user` or `consec`, e.g. admins):

```bash
php artisan portal:reset-data --with-users
```

After a reset, restore from backup or run your seeders as needed. This command **truncates** tables; it does **not** drop them or re-run migrations.

---

# 🚨 TROUBLESHOOTING

Assume you are in the project directory unless noted:

```bash
cd /var/www/boardsmemberportal
```

---

## Database (separate server)

### ❌ Cannot connect / wrong credentials

Test from the **app server** (replace host/user):

```bash
mysql -h 192.168.1.100 -P 3306 -u bmp_user -p
```

Check app `.env` matches: `DB_HOST`, `DB_PORT`, `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD`.

**Typical fixes (on DB server):**

* Firewall: `sudo ufw allow 3306` (and allow app server IP if you restrict sources)
* MySQL `bind-address = 0.0.0.0` in `mysqld.cnf`, then `sudo systemctl restart mysql`
* User host: `'bmp_user'@'%'` (or host matching your app server)

### ❌ `SQLSTATE[HY000] [2002]` — connection refused / timed out

DB host unreachable from the app server. Verify network path and port:

```bash
nc -zv 192.168.1.100 3306
# or
telnet 192.168.1.100 3306
```

### ❌ `SQLSTATE[HY000] [1045]` — access denied

Wrong user/password or user not allowed from the app host. Re-check grants in MySQL and `.env`.

### ❌ Verify Laravel can reach the DB

```bash
php artisan db:show
php artisan migrate:status
```

Optional (interactive):

```bash
php artisan tinker
>>> DB::connection()->getPdo();
```

---

## Laravel caches & config

After changing `.env` or config, clear caches:

```bash
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear
```

One-shot clear (Laravel 11+):

```bash
php artisan optimize:clear
```

**Production** (rebuild caches after fixes):

```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan optimize
```

App info:

```bash
php artisan about
```

---

## Migrations

See status:

```bash
php artisan migrate:status
```

Apply pending migrations:

```bash
php artisan migrate --force
```

**Destructive** — drops all tables and re-runs migrations (loses data):

```bash
php artisan migrate:fresh --seed --force
```

Use only when you intend a full database rebuild (e.g. empty staging).

---

## Permissions & storage

```bash
sudo chown -R www-data:www-data /var/www/boardsmemberportal
sudo chmod -R 775 storage bootstrap/cache
```

Public storage symlink (if missing uploaded/public files):

```bash
php artisan storage:link
```

---

## Frontend / Vite assets

```bash
npm ci
npm run build
```

If `node_modules` is corrupted:

```bash
rm -rf node_modules package-lock.json
npm install
npm run build
```

---

## Nginx & PHP-FPM

Test and reload Nginx:

```bash
sudo nginx -t
sudo systemctl reload nginx
# or
sudo systemctl restart nginx
```

Restart PHP 8.2 FPM after PHP/config changes:

```bash
sudo systemctl restart php8.2-fpm
```

---

## Mail (SMTP)

After changing mail env vars:

```bash
php artisan config:clear
```

Test delivery depends on your setup; ensure outbound SMTP port is allowed from the app server.

---

## Scheduler (cron)

List scheduled tasks:

```bash
php artisan schedule:list
```

Run the scheduler once (manual test):

```bash
php artisan schedule:run
```

---

## Queue workers (if you use `QUEUE_CONNECTION=database` or `redis`)

See failed jobs:

```bash
php artisan queue:failed
```

Process queue (foreground test):

```bash
php artisan queue:work --stop-when-empty
```

In production, use **Supervisor** or **systemd** to keep `queue:work` running (not covered in detail here).

---

## Composer & autoload

```bash
composer install --no-dev --optimize-autoloader
composer dump-autoload -o
```

---

## Project-specific Artisan commands

| Command | Purpose |
|--------|---------|
| `php artisan portal:reset-data` | Truncate portal content (interactive; **destructive**) |
| `php artisan portal:reset-data --with-users` | Same + delete `user` / `consec` accounts (**destructive**) |
| `php artisan admin:create` | Create an admin user (optional args: first name, last name, email, password; `--username=`) |
| `php artisan announcements:publish-scheduled` | Publish due scheduled announcements (also run by cron) |
| `php artisan users:check-idle` | Idle-user check (also run by cron) |
| `php artisan messages:send-unread-reminders` | Unread message reminders (also run by cron) |
| `php artisan messages:delete-all` | Delete all messages (`--force` skips confirmation; **destructive**) |
| `php artisan birthdays:send-greetings` | Birthday emails (also run by cron daily) |
| `php artisan birthdays:send-sample {email}` | Send one sample birthday email to an address |

List every Artisan command:

```bash
php artisan list
```

---

## Logs

```bash
tail -f storage/logs/laravel.log
```

Web server logs (paths may vary):

```bash
sudo tail -f /var/log/nginx/error.log
```

---

# ✅ DEPLOY UPDATE COMMAND

```bash
cd /var/www/boardsmemberportal

git pull origin main

composer install --no-dev --optimize-autoloader
npm install
npm run build

php artisan migrate --seed --force
php artisan optimize
```

---

# 🔐 FINAL CHECKLIST

* DB remote working ✅
* APP_DEBUG=false ✅
* Queue running ✅
* Cron running ✅
* Assets built ✅
* Permissions correct ✅

---
