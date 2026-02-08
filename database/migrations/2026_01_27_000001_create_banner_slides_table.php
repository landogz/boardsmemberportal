<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('banner_slides', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('subtitle')->nullable();
            $table->enum('media_type', ['image', 'video'])->default('image');
            $table->unsignedBigInteger('media_id')->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->foreign('media_id')->references('id')->on('media_library')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('banner_slides');
    }
};
