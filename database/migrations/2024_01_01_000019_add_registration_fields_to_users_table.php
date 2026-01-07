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
            // Government Agency (Foreign Key) - check if column exists first
            if (!Schema::hasColumn('users', 'government_agency_id')) {
                $table->unsignedBigInteger('government_agency_id')->nullable()->after('privilege');
            }
            // Add foreign key if it doesn't exist
            $foreignKeys = DB::select("SELECT CONSTRAINT_NAME FROM information_schema.KEY_COLUMN_USAGE WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'users' AND COLUMN_NAME = 'government_agency_id' AND CONSTRAINT_NAME LIKE '%foreign%'");
            if (empty($foreignKeys)) {
                $table->foreign('government_agency_id')->references('id')->on('government_agencies')->onDelete('set null');
            }
            
            // Name fields
            if (!Schema::hasColumn('users', 'middle_initial')) {
                $table->string('middle_initial', 10)->nullable()->after('first_name');
            }
            
            // Titles
            if (!Schema::hasColumn('users', 'pre_nominal_title')) {
                $table->enum('pre_nominal_title', ['Mr.', 'Ms.'])->nullable()->after('last_name');
            }
            if (!Schema::hasColumn('users', 'post_nominal_title')) {
                $table->string('post_nominal_title')->nullable()->after('pre_nominal_title'); // Can be Sr., Jr., I, II, III, or custom
            }
            
            // Personal Information
            if (!Schema::hasColumn('users', 'designation')) {
                $table->string('designation')->nullable()->after('position');
            }
            if (!Schema::hasColumn('users', 'sex')) {
                $table->enum('sex', ['Male', 'Female'])->nullable()->after('designation');
            }
            if (!Schema::hasColumn('users', 'gender')) {
                $table->enum('gender', ['Male', 'Female', 'Non-Binary'])->nullable()->after('sex');
            }
            if (!Schema::hasColumn('users', 'birth_date')) {
                $table->date('birth_date')->nullable()->after('gender');
            }
            
            // Office Address (PSGC)
            if (!Schema::hasColumn('users', 'office_building_no')) {
                $table->string('office_building_no')->nullable()->after('company');
            }
            if (!Schema::hasColumn('users', 'office_house_no')) {
                $table->string('office_house_no')->nullable()->after('office_building_no');
            }
            if (!Schema::hasColumn('users', 'office_street_name')) {
                $table->string('office_street_name')->nullable()->after('office_house_no');
            }
            if (!Schema::hasColumn('users', 'office_purok')) {
                $table->string('office_purok')->nullable()->after('office_street_name');
            }
            if (!Schema::hasColumn('users', 'office_sitio')) {
                $table->string('office_sitio')->nullable()->after('office_purok');
            }
            if (!Schema::hasColumn('users', 'office_region')) {
                $table->string('office_region')->nullable()->after('office_sitio');
            }
            if (!Schema::hasColumn('users', 'office_province')) {
                $table->string('office_province')->nullable()->after('office_region');
            }
            if (!Schema::hasColumn('users', 'office_city_municipality')) {
                $table->string('office_city_municipality')->nullable()->after('office_province');
            }
            if (!Schema::hasColumn('users', 'office_barangay')) {
                $table->string('office_barangay')->nullable()->after('office_city_municipality');
            }
            
            // Contact Information
            if (!Schema::hasColumn('users', 'landline')) {
                $table->string('landline')->nullable()->after('mobile');
            }
            
            // Username
            if (!Schema::hasColumn('users', 'username')) {
                $table->string('username')->nullable()->unique()->after('email');
            }
            if (!Schema::hasColumn('users', 'username_edited')) {
                $table->boolean('username_edited')->default(false)->after('username');
            }
            
            // Single device login tracking
            if (!Schema::hasColumn('users', 'current_session_id')) {
                $table->string('current_session_id')->nullable()->after('username_edited');
            }
            if (!Schema::hasColumn('users', 'last_activity')) {
                $table->timestamp('last_activity')->nullable()->after('current_session_id');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['government_agency_id']);
            $table->dropColumn([
                'government_agency_id',
                'middle_initial',
                'pre_nominal_title',
                'post_nominal_title',
                'designation',
                'sex',
                'gender',
                'birth_date',
                'office_building_no',
                'office_house_no',
                'office_street_name',
                'office_purok',
                'office_sitio',
                'office_region',
                'office_province',
                'office_city_municipality',
                'office_barangay',
                'landline',
                'username',
                'username_edited',
                'current_session_id',
                'last_activity',
            ]);
        });
    }
};

