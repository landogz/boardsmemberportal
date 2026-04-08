# Deployment Guide

Choose your deployment type (sections below):

- **[How to install Git, Composer, Node, PHP, MySQL](#how-to-install-git-composer-node-js-php-mysql)** – prerequisites for all environments (Windows, Mac, Linux)
- **[Live deployment](#live-deployment)** – production server (Ubuntu/Debian, Nginx, SSH); summary + link to full guide
- **[Full live guide (separate DB, email, cron, troubleshooting)](/deployment-instructions/live)** – step-by-step production install (`DEPLOYMENT_INSTRUCTIONS.md`)
- **[Localhost — Laragon (Windows)](#localhost-laragon-windows)** – local development on Windows using Laragon
- **[Localhost — Mac (MAMP / Manager OS X)](#localhost-mac-mamp-manager-os-x)** – local development on macOS using MAMP or similar

---

## How to install Git, Composer, Node.js, PHP, MySQL

**Composer:** [Download for Windows (Composer-Setup.exe)](https://getcomposer.org/Composer-Setup.exe) | [Download & install (all platforms)](https://getcomposer.org/download/)

Depending on your environment, use the instructions below. **Live server (Linux)** has full commands in the [Live deployment](#live-deployment) full guide.

### Live server (Ubuntu/Debian)

On a production server, install from the terminal (SSH). Full step-by-step commands are in the [full live deployment guide](/deployment-instructions/live). Summary:

| Tool | Install |
|------|--------|
| **Git** | `sudo apt update && sudo apt install -y git` |
| **PHP 8.2+** | `sudo apt install -y php8.2 php8.2-cli php8.2-fpm php8.2-mbstring php8.2-xml php8.2-curl php8.2-zip php8.2-mysql php8.2-intl php8.2-gd` |
| **Composer** | Download from [getcomposer.org](https://getcomposer.org/download/), then `php composer-setup.php --install-dir=/usr/local/bin --filename=composer` |
| **MySQL** | `sudo apt install -y mysql-server` then `sudo mysql_secure_installation` |
| **Node.js & npm** | NodeSource LTS: `curl -fsSL https://deb.nodesource.com/setup_lts.x \| sudo -E bash -` then `sudo apt install -y nodejs` |

Verify: `git --version`, `php -v`, `composer --version`, `node -v`, `npm -v`, and that MySQL is running.

### Windows (Laragon)

[Laragon](https://laragon.org/download/) **6.0 (220916)** Full includes **PHP**, **MySQL**, **Node.js**, and **Git**. Download: [official site](https://laragon.org/download/) or [Laragon WAMP 6.0 (MediaFire)](https://www.mediafire.com/file/o4z34ilyw60pepz/laragon-wamp.exe/file). After installing Laragon:

1. **Git** — Already included. Open Laragon’s terminal (right‑click Laragon → Terminal) and run `git --version`.
2. **Composer** — Often included in Laragon Full. Check with `composer --version` in Laragon’s terminal. If missing:
   - Download [Composer-Setup.exe](https://getcomposer.org/Composer-Setup.exe) and run it.
   - When asked for “PHP executable”, use Laragon’s PHP, e.g. `C:\laragon\bin\php\php-8.2.x-Windows-v16-x64\php.exe` (path may vary by version).
3. **Node.js & npm** — Included in Laragon Full. Verify: `node -v`, `npm -v`.

If anything is missing, use **Laragon → Menu → Quick app** to add PHP/Node versions, or install [Composer](https://getcomposer.org/download/) and [Node.js](https://nodejs.org/) manually.

### Windows (without Laragon)

Install each tool manually:

| Tool | How to install |
|------|----------------|
| **Git** | [git-scm.com/download/win](https://git-scm.com/download/win) — run the installer, use “Git from the command line”. |
| **PHP 8.2+** | [windows.php.net/download](https://windows.php.net/download/) — download “VS16 x64 Non Thread Safe”, extract to e.g. `C:\php`, add to PATH. Enable extensions in `php.ini`: `mbstring`, `xml`, `curl`, `zip`, `openssl`, `pdo_mysql`, `gd`, `fileinfo`. |
| **Composer** | [getcomposer.org/Composer-Setup.exe](https://getcomposer.org/Composer-Setup.exe) — run and point to your PHP executable. |
| **Node.js & npm** | [nodejs.org](https://nodejs.org/) — download LTS installer, run it. |
| **MySQL** | [dev.mysql.com/downloads/installer](https://dev.mysql.com/downloads/installer/) — run MySQL Installer, install MySQL Server and set root password. |

Then open a new terminal and verify: `git --version`, `php -v`, `composer --version`, `node -v`, `npm -v`.

### Mac (Homebrew)

Using [Homebrew](https://brew.sh/) (install from https://brew.sh if needed):

```bash
# Install Git, PHP, MySQL, Node.js
brew install git php mysql node

# Start MySQL on login (optional)
brew services start mysql

# Composer (global)
php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
php composer-setup.php --install-dir=/usr/local/bin --filename=composer
rm composer-setup.php
```

Verify: `git --version`, `php -v`, `composer --version`, `mysql --version`, `node -v`, `npm -v`.

### Mac (MAMP only — PHP & MySQL from MAMP)

If you use **MAMP** for PHP and MySQL only, install the rest separately:

| Tool | How to install |
|------|----------------|
| **PHP & MySQL** | Use MAMP; start Apache and MySQL from MAMP. |
| **Git** | Usually pre‑installed. Check: `git --version`. If missing: install [Xcode Command Line Tools](https://developer.apple.com/xcode/) (`xcode-select --install`) or run `brew install git`. |
| **Composer** | In Terminal: `php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"` then run the installer. Use MAMP’s PHP if needed: `/Applications/MAMP/bin/php/php8.x.x/bin/php composer-setup.php --install-dir=/usr/local/bin --filename=composer`. |
| **Node.js & npm** | [nodejs.org](https://nodejs.org/) LTS installer, or `brew install node`. |

Verify in Terminal: `git --version`, `composer --version`, `node -v`, `npm -v`, and that MAMP’s PHP/MySQL are running.

---

## Live deployment

Deploy the Board Member Portal to a **production Linux server** (e.g. Ubuntu + Nginx + PHP-FPM). The **full guide** is written for an **application server** plus a **separate MySQL server** (replace every example IP with **your real public IP or domain** on the app side and **your real DB host** on the database side).

- **Use when:** Going live with a public URL (`APP_URL` and Nginx `server_name` must match **your** hostname or IP)
- **Full guide covers:** Remote MySQL setup (`bind-address`, user, firewall), app server stack, **`.env` including `DB_*`, `MAIL_*`, and `CONTACT_RECIPIENT_EMAIL`**, migrations, `npm run build`, Nginx root → `public/`, **Laravel cron** (`schedule:run` every minute), optional **`portal:reset-data`** for staging, and a **troubleshooting** command reference

**→ Open the [full live deployment guide](/deployment-instructions/live)** (same content as [DEPLOYMENT_INSTRUCTIONS.md](DEPLOYMENT_INSTRUCTIONS.md) in the repo).

**Quick checklist**
- **Database server (if separate):** MySQL installed, database + user created, remote access from app host (`3306`), firewall rules as needed
- **App server:** SSH in; install Nginx, PHP 8.2-FPM, Composer, Node.js, Git
- **App:** Clone to e.g. `/var/www/boardsmemberportal`; `composer install --no-dev --optimize-autoloader`; `npm ci` or `npm install`; `npm run build`
- **`.env`:** `APP_ENV=production`, `APP_DEBUG=false`, `APP_URL` = your public `http(s)://…` host; `DB_HOST` / `DB_*` reachable from the app; **`MAIL_MAILER=smtp`** and **`MAIL_HOST`**, **`MAIL_USERNAME`**, **`MAIL_PASSWORD`**, **`MAIL_FROM_*`**; **`CONTACT_RECIPIENT_EMAIL`** (single address for Contact Us)
- **Laravel:** `php artisan key:generate` (once), `php artisan migrate --force` (use `--seed` only when you intend to seed)
- **Storage:** `php artisan storage:link`; `chown`/`chmod` on `storage` and `bootstrap/cache` for the web user (e.g. `www-data`)
- **Web:** Nginx (or Apache) document root = `public/`
- **Cron (required for scheduled tasks):** e.g. `* * * * * cd /var/www/boardsmemberportal && /usr/bin/php artisan schedule:run >> /dev/null 2>&1` (adjust path and `php` binary)
- **Real-time / jobs:** If you use Laravel Reverb or `QUEUE_CONNECTION=database`, run the matching processes (e.g. `php artisan reverb:start`, `php artisan queue:work`) under **Supervisor** or **systemd** in production
- **HTTPS:** When you have a domain, terminate TLS (e.g. Let’s Encrypt + Certbot)

---

## Localhost — Laragon (Windows)

Deploy and run the app **locally on Windows** using [Laragon](https://laragon.org/) **6.0 (220916)**.

### Prerequisites

- [Laragon](https://laragon.org/download/) **6.0 (220916)** Full (recommended; includes PHP, MySQL, Node.js, Git) — [download Laragon WAMP from MediaFire](https://www.mediafire.com/file/o4z34ilyw60pepz/laragon-wamp.exe/file)
- [Composer](https://getcomposer.org/download/) ([download Windows .exe](https://getcomposer.org/Composer-Setup.exe)) if not included in your Laragon install

### Steps

1. **Start Laragon**
   - Open Laragon and click **Start All** (Apache/Nginx + MySQL).

2. **Clone or copy the project**
   - Clone into Laragon’s `www` folder, e.g.:
   - `C:\laragon\www\boardsmemberportal`
   ```bash
   cd C:\laragon\www
   git clone https://github.com/landogz/boardsmemberportal.git
   cd boardsmemberportal
   ```

3. **Environment file**
   ```bash
   copy .env.example .env
   php artisan key:generate
   ```
   Edit `.env` for local use:
   ```env
   APP_NAME="Board Member Portal"
   APP_ENV=local
   APP_DEBUG=true
   APP_URL=http://boardsmemberportal.test

   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=boardsmemberportal
   DB_USERNAME=root
   DB_PASSWORD=

   REVERB_HOST=127.0.0.1
   REVERB_PORT=8080
   REVERB_SCHEME=http
   ```
   If you use a different virtual host URL (e.g. `http://localhost/boardsmemberportal`), set `APP_URL` to that.

4. **Create database**
   - Open **MySQL** from Laragon (e.g. HeidiSQL or terminal):
   ```sql
   CREATE DATABASE boardsmemberportal CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
   ```

5. **Dependencies and app setup**
   ```bash
   composer install
   npm install
   npm run dev       # for local development (watches for changes)
   # or for production build:
   # npm run build
   php artisan migrate
   php artisan db:seed --class=RolePermissionSeeder
   php artisan db:seed --class=GovernmentAgencySeeder
   php artisan db:seed --class=UserSeeder
   php artisan storage:link
   ```

6. **Run Reverb (WebSockets) in a separate terminal**
   ```bash
   cd C:\laragon\www\boardsmemberportal
   php artisan reverb:start
   ```
   Keep this window open while developing.

7. **Access the app**
   - If Laragon created a virtual host: **http://boardsmemberportal.test**
   - Or run `php artisan serve` and use **http://localhost:8000**
   - Admin: **http://boardsmemberportal.test/admin/dashboard** (or same with `localhost:8000`)

8. **Optional: Scheduler (cron) on Windows**
   - Use Task Scheduler to run every minute:
   - `php C:\laragon\www\boardsmemberportal\artisan schedule:run`

---

## Localhost — Mac (MAMP / Manager OS X)

Deploy and run the app **locally on macOS** using [MAMP](https://www.mamp.info/) (or similar: MAMP Pro, Manager OS X, or built-in Apache + Homebrew PHP/MySQL).

### Prerequisites

- [MAMP](https://www.mamp.info/en/downloads/) (or MAMP Pro), **or**
- macOS with PHP and MySQL (e.g. Homebrew: `brew install php mysql`)
- [Composer](https://getcomposer.org/)
- [Node.js](https://nodejs.org/) (LTS)

### Steps (MAMP)

1. **Start MAMP**
   - Open MAMP and start **Apache** and **MySQL**.

2. **Project location**
   - Place the project in MAMP’s document root, e.g.:
   - `Applications/MAMP/htdocs/boardsmemberportal`
   ```bash
   cd /Applications/MAMP/htdocs
   git clone https://github.com/landogz/boardsmemberportal.git
   cd boardsmemberportal
   ```

3. **Environment file**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```
   Edit `.env` for local use:
   ```env
   APP_NAME="Board Member Portal"
   APP_ENV=local
   APP_DEBUG=true
   APP_URL=http://localhost:8888/boardsmemberportal/public

   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=8889
   DB_DATABASE=boardsmemberportal
   DB_USERNAME=root
   DB_PASSWORD=root

   REVERB_HOST=127.0.0.1
   REVERB_PORT=8080
   REVERB_SCHEME=http
   ```
   **Note:** MAMP default MySQL port is **8889** and password **root**. Use MAMP’s ports if you didn’t change them. If you use `php artisan serve`, set `APP_URL=http://localhost:8000` and use port **3306** if MySQL is not from MAMP.

4. **Create database**
   - Open **phpMyAdmin** (e.g. http://localhost:8888/phpMyAdmin/) or use terminal:
   ```sql
   CREATE DATABASE boardsmemberportal CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
   ```

5. **Dependencies and app setup**
   ```bash
   composer install
   npm install
   npm run dev       # for local development (watches for changes)
   # or for production build:
   # npm run build
   php artisan migrate
   php artisan db:seed --class=RolePermissionSeeder
   php artisan db:seed --class=GovernmentAgencySeeder
   php artisan db:seed --class=UserSeeder
   php artisan storage:link
   ```

6. **Run Reverb (WebSockets) in a separate terminal**
   ```bash
   cd /Applications/MAMP/htdocs/boardsmemberportal
   php artisan reverb:start
   ```
   Keep this running while developing.

7. **Access the app**
   - **With MAMP Apache:**  
     http://localhost:8888/boardsmemberportal/public  
   - **Or with Artisan:**  
     `php artisan serve` → http://localhost:8000  
   - Admin: add `/admin/dashboard` to the same base URL.

8. **Optional: Scheduler (cron) on Mac**
   ```bash
   crontab -e
   ```
   Add:
   ```cron
   * * * * * cd /Applications/MAMP/htdocs/boardsmemberportal && php artisan schedule:run >> /dev/null 2>&1
   ```

### Steps (Mac without MAMP: Homebrew PHP + MySQL)

- Install PHP and MySQL: `brew install php mysql`
- Start MySQL: `brew services start mysql`
- Use project path of your choice (e.g. `~/Sites/boardsmemberportal`)
- In `.env`: `DB_PORT=3306`, `DB_PASSWORD=` (empty), `APP_URL=http://localhost:8000`
- Run `php artisan serve` and Reverb as above; use **http://localhost:8000**.

---

## Summary

| Deployment        | Doc / section              | APP_URL example              |
|-------------------|----------------------------|------------------------------|
| Live              | [Full live guide](/deployment-instructions/live) · [DEPLOYMENT_INSTRUCTIONS.md](DEPLOYMENT_INSTRUCTIONS.md) | Your **actual** public `https://…` or `http://…` |
| Local — Laragon (Windows) | [Above](#localhost-laragon-windows) | `http://boardsmemberportal.test` or `http://localhost:8000` |
| Local — Mac (MAMP / Manager OS X) | [Above](#localhost-mac-mamp-manager-os-x) | `http://localhost:8888/boardsmemberportal/public` or `http://localhost:8000` |

For **.env** keys (live vs local), see [.env.example](.env.example). For **SMTP**, **cron**, **`portal:reset-data`**, and **troubleshooting commands**, use the [full live guide](/deployment-instructions/live).
