# SSH Tunnel Quick Start Guide

## Important: Where to Run Commands

**The SSH tunnel runs on YOUR LOCAL MACHINE (your Mac), NOT on the remote server.**

The flow is:
```
Your Mac → SSH Server (203.82.37.117) → MySQL Database (173.16.18.126)
```

## Step-by-Step Instructions

### 1. Make sure you're on your LOCAL machine (not SSH'd into the server)

If you're currently logged into the remote server, exit first:
```bash
exit
```

You should see your local prompt like:
```
landogz@Rolans-MacBook-Air boardsmemberportal %
```

### 2. Copy your SSH public key to the server (if not done yet)

Run this on your **local machine**:
```bash
ssh-copy-id -i ~/.ssh/id_rsa_bmp.pub -p 22 bmpdb@203.82.37.117
```

Enter password when prompted: `bmpdb@2025!`

### 3. Test SSH key authentication (from local machine)

```bash
ssh -i ~/.ssh/id_rsa_bmp -p 22 bmpdb@203.82.37.117
```

If it connects without asking for a password, you're good! Type `exit` to return.

### 4. Start the SSH tunnel (from local machine)

```bash
cd /Applications/XAMPP/xamppfiles/htdocs/boardsmemberportal
php database/ssh-tunnel.php start
```

You should see:
```
SSH tunnel established successfully (PID: xxxxx)
Local port: 3307 -> Remote: 173.16.18.126:3306
```

### 5. Check tunnel status (from local machine)

```bash
php database/ssh-tunnel.php status
```

### 6. Test database connection (from local machine)

```bash
php artisan db:show
```

Or:
```bash
php artisan tinker --execute="DB::connection()->getPdo(); echo 'Connection successful!';"
```

## Troubleshooting

### "Command 'php' not found"
- This means you're on the remote server, not your local machine
- Exit the SSH session: type `exit`
- Make sure you're in your local terminal

### "Connection refused"
- Check if tunnel is running: `php database/ssh-tunnel.php status`
- If not running, start it: `php database/ssh-tunnel.php start`
- Check tunnel logs: `tail -f storage/logs/ssh-tunnel.log`

### "Permission denied"
- Make sure SSH key is copied: `ssh-copy-id -i ~/.ssh/id_rsa_bmp.pub -p 22 bmpdb@203.82.37.117`
- Test SSH connection: `ssh -i ~/.ssh/id_rsa_bmp -p 22 bmpdb@203.82.37.117`

## Summary

✅ **Run on LOCAL machine:**
- `php database/ssh-tunnel.php start`
- `php artisan db:show`
- All Laravel commands

❌ **Do NOT run on remote server:**
- The tunnel script
- Laravel artisan commands
- Database connection tests

The remote server is just a "jump host" - the tunnel goes through it to reach the MySQL database.

