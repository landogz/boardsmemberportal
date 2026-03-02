<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Adds Notice of Postponement type and status column (scheduled/postponed).
     */
    public function up(): void
    {
        Schema::table('notices', function (Blueprint $table) {
            $table->string('status', 32)->nullable()->default('scheduled')->after('description');
        });

        DB::statement("ALTER TABLE notices MODIFY COLUMN notice_type ENUM('Notice of Meeting', 'Agenda', 'Notice of Postponement', 'Other Matters', 'Board Issuances') DEFAULT 'Notice of Meeting'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('notices', function (Blueprint $table) {
            $table->dropColumn('status');
        });

        DB::statement("ALTER TABLE notices MODIFY COLUMN notice_type ENUM('Notice of Meeting', 'Agenda', 'Other Matters', 'Board Issuances') DEFAULT 'Notice of Meeting'");
    }
};
