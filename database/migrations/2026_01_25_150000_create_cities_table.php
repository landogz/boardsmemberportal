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
        Schema::create('cities', function (Blueprint $table) {
            $table->id();
            $table->string('city_code', 20)->unique();
            $table->string('city_name');
            $table->string('psgc_code', 20);
            $table->string('province_code', 20);
            $table->string('region_code', 10);
            $table->timestamps();
            
            $table->index('province_code');
            $table->index('region_code');
            $table->index('city_code');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cities');
    }
};
