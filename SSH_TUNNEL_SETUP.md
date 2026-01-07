# SSH Tunnel Setup for Database Connection

## Overview
The database requires SSH tunneling to connect. The MySQL server (`173.16.18.126`) is accessed through an SSH tunnel via `203.82.37.117:22`.

## Configuration
The SSH tunnel configuration is stored in `.env`:
```
DB_SSH_TUNNEL=true
DB_SSH_HOST=203.82.37.117
DB_SSH_PORT=22
DB_SSH_USER=bmpdb
DB_SSH_PASSWORD=bmpdb@2025!
DB_SSH_REMOTE_HOST=173.16.18.126
DB_SSH_REMOTE_PORT=3306
DB_SSH_LOCAL_PORT=3307
```

## Setup Options

### Option 1: SSH Key Authentication (Recommended)
1. Generate SSH key if you don't have one:
   ```bash
   ssh-keygen -t rsa -b 4096 -C "your_email@example.com"
   ```

2. Copy your public key to the SSH server:
   ```bash
   ssh-copy-id -p 22 bmpdb@203.82.37.117
   ```

3. Test SSH connection:
   ```bash
   ssh -p 22 bmpdb@203.82.37.117
   ```

### Option 2: Install sshpass (for password authentication)
```bash
# macOS
brew install hudochenkov/sshpass/sshpass

# Linux
sudo apt-get install sshpass
```

### Option 3: Install expect
```bash
# macOS
brew install expect

# Linux
sudo apt-get install expect
```

## Usage

### Start SSH Tunnel
```bash
php database/ssh-tunnel.php start
```

### Stop SSH Tunnel
```bash
php database/ssh-tunnel.php stop
```

### Restart SSH Tunnel
```bash
php database/ssh-tunnel.php restart
```

### Check Tunnel Status
```bash
php database/ssh-tunnel.php status
```

## Testing Database Connection

After starting the SSH tunnel, test the database connection:
```bash
php artisan tinker --execute="DB::connection()->getPdo(); echo 'Connection successful!';"
```

Or:
```bash
php artisan db:show
```

## Troubleshooting

1. **Port already in use**: Change `DB_SSH_LOCAL_PORT` in `.env` to a different port (e.g., 3308)

2. **Connection refused**: 
   - Verify SSH credentials are correct
   - Check if SSH server is accessible: `ping 203.82.37.117`
   - Verify SSH port: `telnet 203.82.37.117 22`

3. **Permission denied**: 
   - Set up SSH key authentication (Option 1)
   - Or install sshpass/expect (Options 2/3)

4. **Tunnel dies**: The tunnel script will need to be restarted. Consider setting up a system service or using `screen`/`tmux` to keep it running.

## Auto-start on Server Boot (Optional)

To automatically start the SSH tunnel when the server boots, you can:
1. Add to crontab with `@reboot`
2. Create a systemd service (Linux)
3. Use launchd (macOS)

