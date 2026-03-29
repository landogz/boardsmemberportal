<?php

return [

    /*
    |--------------------------------------------------------------------------
    | mysqldump binary
    |--------------------------------------------------------------------------
    |
    | Path to the mysqldump executable. Defaults to "mysqldump" on PATH.
    | On XAMPP for macOS you might use e.g.:
    | /Applications/XAMPP/xamppfiles/bin/mysqldump
    |
    */

    'mysqldump_path' => env('DB_BACKUP_MYSQLDUMP_PATH', 'mysqldump'),

    /*
    |--------------------------------------------------------------------------
    | Backup storage (under storage/app)
    |--------------------------------------------------------------------------
    */

    'storage_subdir' => 'backups/database',

];
