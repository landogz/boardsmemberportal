# MySQL Database Setup for Laravel

## Configuration Steps

Since you're using MySQL with XAMPP, follow these steps:

### 1. Update your `.env` file

Open the `.env` file in the root directory and update the database configuration:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=boardsmemberportal
DB_USERNAME=root
DB_PASSWORD=
```

**Note:** 
- If your XAMPP MySQL has a password, add it to `DB_PASSWORD`
- Replace `boardsmemberportal` with your actual database name
- Default XAMPP MySQL username is `root` with no password

### 2. Create the Database

Open phpMyAdmin (usually at `http://localhost/phpmyadmin`) and create a new database:

```sql
CREATE DATABASE boardsmemberportal CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

Or use MySQL command line:
```bash
mysql -u root -p
CREATE DATABASE boardsmemberportal CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
EXIT;
```

### 3. Run Migrations

After configuring the database, run the migrations:

```bash
php artisan migrate
```

### 4. Test the Connection

You can test the database connection by running:

```bash
php artisan tinker
```

Then in tinker:
```php
DB::connection()->getPdo();
```

If it returns a PDO object, your connection is working!

## XAMPP MySQL Default Settings

- **Host:** 127.0.0.1 or localhost
- **Port:** 3306
- **Username:** root
- **Password:** (usually empty, but check your XAMPP setup)

## Troubleshooting

If you get connection errors:

1. Make sure MySQL is running in XAMPP Control Panel
2. Check that the database name in `.env` matches the one you created
3. Verify your MySQL username and password
4. Check that the MySQL port (3306) is correct

