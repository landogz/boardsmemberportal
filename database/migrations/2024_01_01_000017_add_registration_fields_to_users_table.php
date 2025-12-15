<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Government Agency (Foreign Key)
            $table->unsignedBigInteger('government_agency_id')->nullable()->after('privilege');
            $table->foreign('government_agency_id')->references('id')->on('government_agencies')->onDelete('set null');
            
            // Name fields
            $table->string('middle_initial', 10)->nullable()->after('first_name');
            
            // Titles
            $table->enum('pre_nominal_title', ['Mr.', 'Ms.'])->nullable()->after('last_name');
            $table->string('post_nominal_title')->nullable()->after('pre_nominal_title'); // Can be Sr., Jr., I, II, III, or custom
            
            // Personal Information
            $table->string('designation')->nullable()->after('position');
            $table->enum('sex', ['Male', 'Female'])->nullable()->after('designation');
            $table->enum('gender', ['Male', 'Female', 'Non-Binary'])->nullable()->after('sex');
            $table->date('birth_date')->nullable()->after('gender');
            
            // Office Address (PSGC)
            $table->string('office_building_no')->nullable()->after('company');
            $table->string('office_house_no')->nullable()->after('office_building_no');
            $table->string('office_street_name')->nullable()->after('office_house_no');
            $table->string('office_purok')->nullable()->after('office_street_name');
            $table->string('office_sitio')->nullable()->after('office_purok');
            $table->string('office_region')->nullable()->after('office_sitio');
            $table->string('office_province')->nullable()->after('office_region');
            $table->string('office_city_municipality')->nullable()->after('office_province');
            $table->string('office_barangay')->nullable()->after('office_city_municipality');
            
            // Contact Information
            $table->string('landline')->nullable()->after('mobile');
            
            // Username
            $table->string('username')->nullable()->unique()->after('email');
            $table->boolean('username_edited')->default(false)->after('username');
            
            // Single device login tracking
            $table->string('current_session_id')->nullable()->after('username_edited');
            $table->timestamp('last_activity')->nullable()->after('current_session_id');
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

