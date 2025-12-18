<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Adjust existing columns to store UUID/string IDs instead of integers
        DB::statement('ALTER TABLE audit_logs MODIFY user_id VARCHAR(36) NULL');
        DB::statement('ALTER TABLE audit_logs MODIFY model_id VARCHAR(36) NULL');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert to unsigned big integer if needed (may fail if data is non-numeric)
        DB::statement('ALTER TABLE audit_logs MODIFY user_id BIGINT UNSIGNED NULL');
        DB::statement('ALTER TABLE audit_logs MODIFY model_id BIGINT UNSIGNED NULL');
    }
};


