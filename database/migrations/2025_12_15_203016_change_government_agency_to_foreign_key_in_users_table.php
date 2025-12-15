<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Drop the old enum column if it exists
            if (Schema::hasColumn('users', 'government_agency')) {
                $table->dropColumn('government_agency');
            }
            
            // Add the new foreign key column if it doesn't exist
            if (!Schema::hasColumn('users', 'government_agency_id')) {
                $table->unsignedBigInteger('government_agency_id')->nullable()->after('privilege');
                $table->foreign('government_agency_id')->references('id')->on('government_agencies')->onDelete('set null');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Drop foreign key and column
            if (Schema::hasColumn('users', 'government_agency_id')) {
                $table->dropForeign(['government_agency_id']);
                $table->dropColumn('government_agency_id');
            }
            
            // Restore enum column
            if (!Schema::hasColumn('users', 'government_agency')) {
                $table->enum('government_agency', ['CONSEC', 'MISD'])->nullable()->after('privilege');
            }
        });
    }
};
