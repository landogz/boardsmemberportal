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
        // Add 'abstain' option to the referendum_votes.vote enum
        DB::statement("ALTER TABLE referendum_votes MODIFY COLUMN vote ENUM('accept','decline','abstain') NOT NULL");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert to original enum options if needed
        DB::statement("ALTER TABLE referendum_votes MODIFY COLUMN vote ENUM('accept','decline') NOT NULL");
    }
};

