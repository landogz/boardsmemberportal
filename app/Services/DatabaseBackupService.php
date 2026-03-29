<?php

namespace App\Services;

use Illuminate\Support\Facades\File;
use Symfony\Component\Process\Process;

class DatabaseBackupService
{
    public function backupDirectory(): string
    {
        $relative = config('database-backup.storage_subdir', 'backups/database');
        $dir = storage_path('app/'.$relative);
        if (! is_dir($dir)) {
            File::makeDirectory($dir, 0750, true);
        }

        return $dir;
    }

    /**
     * @return array<int, array{filename: string, size: int, modified_at: string}>
     */
    public function listBackups(): array
    {
        $dir = $this->backupDirectory();
        $paths = File::glob($dir.DIRECTORY_SEPARATOR.'backup_*');
        if (! $paths) {
            return [];
        }

        $items = [];
        foreach ($paths as $path) {
            if (! is_file($path)) {
                continue;
            }
            $filename = basename($path);
            if (! $this->isAllowedFilename($filename)) {
                continue;
            }
            $items[] = [
                'filename' => $filename,
                'size' => filesize($path) ?: 0,
                'modified_at' => date('c', filemtime($path) ?: time()),
            ];
        }

        usort($items, fn ($a, $b) => strcmp($b['modified_at'], $a['modified_at']));

        return $items;
    }

    public function resolveBackupPath(string $filename): ?string
    {
        if (! $this->isAllowedFilename($filename)) {
            return null;
        }
        $path = $this->backupDirectory().DIRECTORY_SEPARATOR.$filename;

        return is_file($path) ? $path : null;
    }

    /**
     * @return array{success: bool, message: string, filename?: string, size?: int}
     */
    public function createBackup(): array
    {
        $name = 'backup_'.date('Y-m-d_His');
        $connectionName = config('database.default');
        $config = config('database.connections.'.$connectionName);

        if (! is_array($config) || empty($config['driver'])) {
            return ['success' => false, 'message' => 'Database connection is not configured.'];
        }

        $driver = $config['driver'];

        if ($driver === 'sqlite') {
            return $this->backupSqlite($config, $name);
        }

        if (in_array($driver, ['mysql', 'mariadb'], true)) {
            return $this->backupMysqlFamily($config, $name);
        }

        return ['success' => false, 'message' => 'Backup is only supported for MySQL, MariaDB, and SQLite.'];
    }

    /**
     * @param  array<string, mixed>  $config
     * @return array{success: bool, message: string, filename?: string, size?: int}
     */
    private function backupSqlite(array $config, string $baseName): array
    {
        $source = $config['database'] ?? '';
        if ($source === '' || ! is_file($source)) {
            return ['success' => false, 'message' => 'SQLite database file not found.'];
        }

        $filename = $baseName.'.sqlite';
        $dest = $this->backupDirectory().DIRECTORY_SEPARATOR.$filename;

        if (! @copy($source, $dest)) {
            return ['success' => false, 'message' => 'Could not copy the SQLite database file.'];
        }

        $size = filesize($dest) ?: 0;

        return [
            'success' => true,
            'message' => 'Backup completed successfully.',
            'filename' => $filename,
            'size' => $size,
        ];
    }

    /**
     * @param  array<string, mixed>  $config
     * @return array{success: bool, message: string, filename?: string, size?: int}
     */
    private function backupMysqlFamily(array $config, string $baseName): array
    {
        $database = $config['database'] ?? '';
        if ($database === '') {
            return ['success' => false, 'message' => 'Database name is not configured.'];
        }

        $mysqldump = $this->resolveMysqldumpBinary();
        if ($mysqldump === null) {
            return [
                'success' => false,
                'message' => 'mysqldump was not found. PHP often has no MySQL tools on PATH. Set DB_BACKUP_MYSQLDUMP_PATH in .env to the full path (e.g. /Applications/XAMPP/xamppfiles/bin/mysqldump on macOS XAMPP).',
            ];
        }

        $filename = $baseName.'.sql';
        $fullPath = $this->backupDirectory().DIRECTORY_SEPARATOR.$filename;

        $tmpFile = tempnam(sys_get_temp_dir(), 'dbbk_');
        if ($tmpFile === false) {
            return ['success' => false, 'message' => 'Could not create a temporary credentials file.'];
        }

        try {
            $ini = $this->buildMysqlClientIni($config);
            file_put_contents($tmpFile, $ini);
            @chmod($tmpFile, 0600);

            $command = array_merge(
                [$mysqldump, '--defaults-extra-file='.$tmpFile, '--single-transaction', '--routines', '--events', '--no-tablespaces'],
                [$database]
            );

            $process = new Process($command);
            $process->setTimeout(3600);
            $process->run();

            if (! $process->isSuccessful()) {
                $err = trim($process->getErrorOutput() ?: $process->getOutput());

                return ['success' => false, 'message' => $err !== '' ? $err : 'mysqldump failed.'];
            }

            $output = $process->getOutput();
            if ($output === '') {
                return ['success' => false, 'message' => 'mysqldump produced no output. Check DB credentials and mysqldump path (DB_BACKUP_MYSQLDUMP_PATH).'];
            }

            if (file_put_contents($fullPath, $output) === false) {
                return ['success' => false, 'message' => 'Could not write backup file.'];
            }

            $size = filesize($fullPath) ?: 0;

            return [
                'success' => true,
                'message' => 'Backup completed successfully.',
                'filename' => $filename,
                'size' => $size,
            ];
        } finally {
            if (is_file($tmpFile)) {
                @unlink($tmpFile);
            }
        }
    }

    /**
     * @param  array<string, mixed>  $config
     */
    private function buildMysqlClientIni(array $config): string
    {
        $lines = ['[client]', 'user='.$config['username']];
        $lines[] = 'password='.($config['password'] ?? '');

        if (! empty($config['unix_socket'])) {
            $lines[] = 'socket='.$config['unix_socket'];
        } else {
            $lines[] = 'host='.($config['host'] ?? '127.0.0.1');
            $lines[] = 'port='.(string) ($config['port'] ?? '3306');
        }

        return implode("\n", $lines)."\n";
    }

    private function isAllowedFilename(string $filename): bool
    {
        return (bool) preg_match('/^backup_\d{4}-\d{2}-\d{2}_\d{6}\.(sql|sqlite)$/', $filename);
    }
}
