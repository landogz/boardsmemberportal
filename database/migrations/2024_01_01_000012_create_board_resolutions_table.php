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
        Schema::create('board_resolutions', function (Blueprint $table) {
            $table->id();
            $table->string('resolution_number')->unique();
            $table->string('title');
            $table->text('description')->nullable();
            $table->unsignedBigInteger('pdf_file')->nullable(); // media_id
            $table->foreign('pdf_file')->references('id')->on('media_library')->onDelete('set null');
            $table->string('category')->nullable();
            $table->date('approved_date')->nullable();
            $table->uuid('uploaded_by')->nullable();
            $table->foreign('uploaded_by')->references('id')->on('users')->onDelete('set null');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('board_resolutions');
    }
};

