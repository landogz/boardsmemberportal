<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Expand allowed values for pre_nominal_title on users table.
     */
    public function up(): void
    {
        $driver = DB::getDriverName();
        if ($driver === 'mysql') {
            DB::statement("ALTER TABLE `users` MODIFY `pre_nominal_title` ENUM('Mr.','Ms.','Secretary','Undersecretary','Director General','Attorney','Executive Director','Dr.','Assistant Secretary','Atty.','Engr.') NULL");
        }
    }

    /**
     * Revert to original limited enum values.
     */
    public function down(): void
    {
        $driver = DB::getDriverName();
        if ($driver === 'mysql') {
            DB::statement("ALTER TABLE `users` MODIFY `pre_nominal_title` ENUM('Mr.','Ms.') NULL");
        }
    }
};

