<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Change pre_nominal_title from ENUM to VARCHAR so we can store
     * both standard titles and custom \"Others\" values (e.g. user-entered text).
     */
    public function up(): void
    {
        $driver = DB::getDriverName();
        if ($driver === 'mysql') {
            DB::statement('ALTER TABLE users MODIFY pre_nominal_title VARCHAR(255) NULL');
        }
    }

    /**
     * Reverse the migrations.
     *
     * Revert to the last known ENUM definition if needed.
     */
    public function down(): void
    {
        $driver = DB::getDriverName();
        if ($driver === 'mysql') {
            DB::statement("ALTER TABLE `users` MODIFY `pre_nominal_title` ENUM('Mr.','Ms.','Secretary','Undersecretary','Director General','Attorney','Executive Director','Dr.','Assistant Secretary','Atty.','Engr.') NULL");
        }
    }
};

