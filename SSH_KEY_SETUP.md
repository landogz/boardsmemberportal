# SSH Key Setup Instructions

## Step 1: SSH Key Generated ✅
Your SSH key has been generated at: `~/.ssh/id_rsa_bmp`

## Step 2: Copy Public Key to Server

You need to manually copy your public key to the SSH server. Here are two methods:

### Method 1: Manual Copy (Recommended)

1. **Display your public key:**
   ```bash
   cat ~/.ssh/id_rsa_bmp.pub
   ```

2. **Copy the entire output** (it starts with `ssh-rsa` and ends with `bmp-database-tunnel`)

3. **SSH into the server** (you'll be prompted for password: `bmpdb@2025!`):
   ```bash
   ssh -p 22 bmpdb@203.82.37.117
   ```

4. **Once connected, run these commands:**
   ```bash
   mkdir -p ~/.ssh
   chmod 700 ~/.ssh
   echo "PASTE_YOUR_PUBLIC_KEY_HERE" >> ~/.ssh/authorized_keys
   chmod 600 ~/.ssh/authorized_keys
   exit
   ```

5. **Test the connection** (should connect without password):
   ```bash
   ssh -i ~/.ssh/id_rsa_bmp -p 22 bmpdb@203.82.37.117
   ```

### Method 2: Using ssh-copy-id (Interactive)

Run this command and enter the password when prompted (`bmpdb@2025!`):
```bash
ssh-copy-id -i ~/.ssh/id_rsa_bmp.pub -p 22 bmpdb@203.82.37.117
```

## Step 3: Test SSH Tunnel

After the key is set up, test the SSH tunnel:
```bash
php database/ssh-tunnel.php start
php database/ssh-tunnel.php status
```

## Step 4: Test Database Connection

Once the tunnel is running:
```bash
php artisan db:show
```

## Your Public Key

Your public key is:
```
ssh-rsa AAAAB3NzaC1yc2EAAAADAQABAAACAQDRQrGPpJQwZ7oe5CXcR3X2hD8veH658IEjorPHokwDb3gGUuErLLkOP1/ekaa1STfU+UszZCJUlkHbaPa8Oyys2DMzrv638AYsYzw8UtNgJVqnw8wm3IlGeC0yDtyB4q63x8Ft91NehxzvVLUKDLjcQ1y80UQ0Se0Ul6zU6d6bKTbNL1SrNAfPh4AhrQ4CKwtb7EoyATgupwa9d6xfX1PsM3+NNJDDohuAC6U1B2lyTaneBhPeQTpIZu5OG39VSK1Jhr3WWIWHaJO3r+4KRRQPL5eaXOFQQVsNpHKT0vk07Ci4Fc4stio9/dSY6TC+6Mn5dHKFhGawa/hzQ0pu/YBFtMH9+1T1McOR/R4TtQVwLxndgRmwz2vaQdfGeiaaIe6YNwYfpw296j78umBeUKeXKwLeirnoOElqS5dyGbhE9UVKfqU6I6ehH4fpY78Ixgt4OTNIjZ1MuC671EJPa3H3vdKsVILpVjbzspN4th9d+b2mOG0kmZWwgR7O3OvWnRoZFshF6Gv+PWe8tmBT3RHltSV9EOMS+ei3ABbcBUMrc0fiZpwlRGcd1zbXdbxqFHgaISPOZXZ1A7nmvPDe2Rx+oSbMyhLuQO7gHrk57i7/qiyVlRpL6f0Q9er9iAs4mqj5l6Vn6VQBSTprUROOCjZXZ2iAz6pb0LTr7kXMD0z0/w== bmp-database-tunnel
```

Copy this entire line and paste it into the `authorized_keys` file on the server.

