# BMP — Step-by-Step: Clear Portal Data

**Application:** Board Members Portal (BMP)  
**Server path:** `/var/www/bmpse.ddb.gov.ph/boardsmemberportal/`  
**Server IP(s):** `173.16.18.125` / `116.50.254.36`  
**SSH user:** `bmpap`

> **Warning:** Clearing data is **permanent**. Always take a database backup before running any command below. Use only for staging, demos, or approved maintenance.

---

## Before you start

1. Get **approval** from the person responsible for BMP / DDB IT.
2. Confirm you are on the **correct server** (staging vs production).
3. **Back up the database** (ask DDB IT if you do not have direct DB access).

---

## Step 1 — Connect to the server

From your computer, open a terminal and SSH in:

```bash
ssh bmpap@173.16.18.125
```

If that IP does not work, try:

```bash
ssh bmpap@116.50.254.36
```

Enter the BMP server password when prompted.

---

## Step 2 — Go to the project folder

```bash
cd /var/www/bmpse.ddb.gov.ph/boardsmemberportal/
```

Confirm you are in the right place:

```bash
pwd
ls artisan
```

You should see `artisan` in the listing.

---

## Step 3 — (Recommended) Back up the database

If you have database access, export a backup **before** clearing data.  
If not, ask DDB IT to run a backup first.

Example (adjust DB name/user/host to match `.env`):

```bash
mysqldump -u DB_USER -p DB_NAME > ~/bmp_backup_$(date +%Y%m%d_%H%M%S).sql
```

---

## Step 4 — Clear core portal data

This runs the built-in Laravel command:

```bash
php artisan portal:reset-data
```

When prompted:

```
DANGER: This will wipe a lot of data from the portal.
 Are you sure you want to continue? (yes/no) [no]:
```

Type **`yes`** and press **Enter**.

### What this clears

| Cleared | Not cleared |
|--------|-------------|
| Attendance confirmations | Admin users |
| Agenda inclusion requests | Board regulations |
| Reference materials | Board resolutions (`official_documents`) |
| Announcements | User accounts (unless `--with-users`) |
| All notices (Agenda, Notice of Meeting, Board Issuances, etc.) | Media library files on disk |
| Referendums (votes, comments, access) | |
| Banner slides | |
| Chat / messaging (direct + group) | |

You should see:

```
Portal data reset completed successfully.
```

---

## Step 5 — (Optional) Also remove Board Member & CONSEC users

Use this only if you also want to delete **non-admin** user accounts (`privilege` = `user` or `consec`):

```bash
php artisan portal:reset-data --with-users
```

Type **`yes`** when prompted.

**Admin accounts are kept.**

---

## Step 6 — (Optional) Also clear Board Regulations & Board Resolutions

`portal:reset-data` does **not** remove regulations or resolutions.  
Run this only if you need those cleared too:

```bash
php artisan tinker --execute="
use Illuminate\Support\Facades\DB;

DB::statement('SET FOREIGN_KEY_CHECKS=0');

DB::table('board_regulation_versions')->truncate();
DB::table('board_regulations')->truncate();
DB::table('official_document_versions')->truncate();
DB::table('official_documents')->truncate();

DB::statement('SET FOREIGN_KEY_CHECKS=1');

echo \"Board regulations and resolutions cleared.\n\";
"
```

---

## Step 7 — Verify

1. Open the BMP site in a browser and confirm lists are empty (notices, referendums, announcements, etc.).
2. Log in as **admin** and check admin modules.
3. If something still appears, confirm cache is not serving old data:

```bash
php artisan optimize:clear
```

---

## Quick reference

| Goal | Command |
|------|---------|
| Clear core portal data | `php artisan portal:reset-data` |
| Clear data + remove board members / CONSEC users | `php artisan portal:reset-data --with-users` |
| Clear regulations & resolutions only | See **Step 6** |
| Clear Laravel cache after reset | `php artisan optimize:clear` |

---

## Troubleshooting

| Problem | What to try |
|---------|-------------|
| `Could not open input file: artisan` | Run `cd /var/www/bmpse.ddb.gov.ph/boardsmemberportal/` first |
| Permission denied | Contact DDB IT — you may need `sudo` or a different user |
| Database connection error | Check `.env` DB settings; contact DDB IT |
| Command not found | Use full path: `/usr/bin/php artisan portal:reset-data` (path may vary) |

---

## Security note

- Do **not** store server passwords in this file or in git.
- Change passwords if they have been shared in chat or email.
- Restrict who can SSH to the BMP server and who can run reset commands.
