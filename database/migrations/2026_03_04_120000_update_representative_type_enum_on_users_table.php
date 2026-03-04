<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (Schema::hasColumn('users', 'representative_type')) {
            DB::statement("
                ALTER TABLE users
                MODIFY representative_type ENUM(
                    'Board Member',
                    'Authorized Representative',
                    'Ex-Officio Member'
                ) NULL
            ");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('users', 'representative_type')) {
            DB::statement("
                ALTER TABLE users
                MODIFY representative_type ENUM(
                    'Board Member',
                    'Authorized Representative'
                ) NULL
            ");
        }
    }
};

