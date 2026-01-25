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
        Schema::create('barangays', function (Blueprint $table) {
            $table->id();
            $table->string('brgy_code', 20)->unique();
            $table->string('brgy_name');
            $table->string('city_code', 20);
            $table->string('province_code', 20);
            $table->string('region_code', 10);
            $table->timestamps();
            
            $table->index('city_code');
            $table->index('province_code');
            $table->index('region_code');
            $table->index('brgy_code');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('barangays');
    }
};
