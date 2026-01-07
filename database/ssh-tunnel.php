<?php

/**
 * SSH Tunnel Manager for Database Connection
 * 
 * This script establishes an SSH tunnel to access the MySQL database
 * through the SSH server.
 */

// Load Laravel environment first
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$config = [
    'ssh_host' => env('DB_SSH_HOST', '203.82.37.117'),
    'ssh_port' => env('DB_SSH_PORT', 22),
    'ssh_user' => env('DB_SSH_USER', 'bmpdb'),
    'ssh_password' => env('DB_SSH_PASSWORD', ''),
    'remote_host' => env('DB_SSH_REMOTE_HOST', '173.16.18.126'),
    'remote_port' => env('DB_SSH_REMOTE_PORT', 3306),
    'local_port' => env('DB_SSH_LOCAL_PORT', 3307),
];

function establishTunnel($config) {
    $pidFile = storage_path('app/ssh-tunnel.pid');
    $logFile = storage_path('logs/ssh-tunnel.log');
    
    // Check if tunnel already exists
    if (file_exists($pidFile)) {
        $pid = (int)file_get_contents($pidFile);
        if (posix_kill($pid, 0)) {
            echo "SSH tunnel already running (PID: $pid)\n";
            return $pid;
        } else {
            // PID file exists but process is dead
            unlink($pidFile);
        }
    }
    
    // Check if local port is already in use
    $connection = @fsockopen('127.0.0.1', $config['local_port'], $errno, $errstr, 1);
    if ($connection) {
        fclose($connection);
        echo "Port {$config['local_port']} is already in use. Please free it or change DB_SSH_LOCAL_PORT.\n";
        return false;
    }
    
    // Check for custom SSH key
    $sshKeyFile = env('DB_SSH_KEY_FILE', '');
    $sshKeyOption = '';
    if (!empty($sshKeyFile) && file_exists($sshKeyFile)) {
        $sshKeyOption = sprintf('-i %s ', escapeshellarg($sshKeyFile));
    } elseif (file_exists(getenv('HOME') . '/.ssh/id_rsa_bmp')) {
        $sshKeyOption = sprintf('-i %s ', escapeshellarg(getenv('HOME') . '/.ssh/id_rsa_bmp'));
    }
    
    // Build SSH command
    $sshCommand = sprintf(
        "ssh %s-N -L %d:%s:%d -p %d %s@%s 2>&1",
        $sshKeyOption,
        $config['local_port'],
        $config['remote_host'],
        $config['remote_port'],
        $config['ssh_port'],
        $config['ssh_user'],
        $config['ssh_host']
    );
    
    // Use sshpass if available
    if (shell_exec('which sshpass')) {
        $sshCommand = sprintf(
            "sshpass -p '%s' %s",
            escapeshellarg($config['ssh_password']),
            $sshCommand
        );
    } else {
        // Try using expect if available
        if (shell_exec('which expect')) {
            $expectScript = sprintf(
                "#!/usr/bin/expect -f\nspawn %s\nexpect \"password:\"\nsend \"%s\\r\"\nexpect eof",
                $sshCommand,
                $config['ssh_password']
            );
            $expectFile = storage_path('app/ssh-tunnel-expect.exp');
            file_put_contents($expectFile, $expectScript);
            chmod($expectFile, 0755);
            $sshCommand = "expect " . escapeshellarg($expectFile);
        } else {
            echo "Warning: sshpass or expect not found. Please install one of them:\n";
            echo "  macOS: brew install hudochenkov/sshpass/sshpass\n";
            echo "  Or: brew install expect\n";
            echo "  Or set up SSH key authentication (recommended)\n";
            echo "\nAttempting connection (you may be prompted for password)...\n";
        }
    }
    
    // Start tunnel in background
    $command = sprintf(
        "nohup %s >> %s 2>&1 & echo $!",
        $sshCommand,
        escapeshellarg($logFile)
    );
    
    $pid = (int)shell_exec($command);
    
    if ($pid > 0) {
        file_put_contents($pidFile, $pid);
        sleep(2); // Wait for tunnel to establish
        
        // Verify tunnel is working
        $test = @fsockopen('127.0.0.1', $config['local_port'], $errno, $errstr, 2);
        if ($test) {
            fclose($test);
            echo "SSH tunnel established successfully (PID: $pid)\n";
            echo "Local port: {$config['local_port']} -> Remote: {$config['remote_host']}:{$config['remote_port']}\n";
            return $pid;
        } else {
            echo "Failed to establish tunnel. Check logs: $logFile\n";
            return false;
        }
    } else {
        echo "Failed to start SSH tunnel\n";
        return false;
    }
}

function stopTunnel() {
    $pidFile = storage_path('app/ssh-tunnel.pid');
    
    if (file_exists($pidFile)) {
        $pid = (int)file_get_contents($pidFile);
        if (posix_kill($pid, 0)) {
            posix_kill($pid, SIGTERM);
            unlink($pidFile);
            echo "SSH tunnel stopped (PID: $pid)\n";
            return true;
        } else {
            unlink($pidFile);
            echo "Tunnel process not found, cleaned up PID file\n";
            return true;
        }
    }
    
    echo "No active tunnel found\n";
    return false;
}

// Handle command line arguments
$action = $argv[1] ?? 'start';

switch ($action) {
    case 'start':
        establishTunnel($config);
        break;
    case 'stop':
        stopTunnel();
        break;
    case 'restart':
        stopTunnel();
        sleep(1);
        establishTunnel($config);
        break;
    case 'status':
        $pidFile = storage_path('app/ssh-tunnel.pid');
        if (file_exists($pidFile)) {
            $pid = (int)file_get_contents($pidFile);
            if (posix_kill($pid, 0)) {
                echo "SSH tunnel is running (PID: $pid)\n";
            } else {
                echo "SSH tunnel PID file exists but process is not running\n";
            }
        } else {
            echo "SSH tunnel is not running\n";
        }
        break;
    default:
        echo "Usage: php database/ssh-tunnel.php [start|stop|restart|status]\n";
        break;
}

