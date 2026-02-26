<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Change gender from enum to string to support expanded options:
     * Lesbian, Gay, Bisexual, Transgender, Queer, Intersex, Non-binary, Prefer not to say
     */
    public function up(): void
    {
        $driver = DB::getDriverName();
        if ($driver === 'mysql') {
            DB::statement('ALTER TABLE users MODIFY gender VARCHAR(64) NULL');
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $driver = DB::getDriverName();
        if ($driver === 'mysql') {
            DB::statement("ALTER TABLE users MODIFY gender ENUM('Male', 'Female', 'Non-Binary') NULL");
        }
    }
};
