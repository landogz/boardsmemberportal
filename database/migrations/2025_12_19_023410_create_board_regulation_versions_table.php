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
        Schema::create('board_regulation_versions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('board_regulation_id');
            $table->foreign('board_regulation_id')->references('id')->on('board_regulations')->onDelete('cascade');
            $table->unsignedBigInteger('pdf_file')->nullable(); // media_id - old file
            $table->foreign('pdf_file')->references('id')->on('media_library')->onDelete('set null');
            $table->string('version')->nullable(); // Version at time of change
            $table->string('title'); // Snapshot of title
            $table->text('description')->nullable(); // Snapshot of description
            $table->date('effective_date')->nullable(); // Snapshot of effective_date
            $table->date('approved_date')->nullable(); // Snapshot of approved_date
            $table->uuid('uploaded_by')->nullable(); // Who made the change
            $table->foreign('uploaded_by')->references('id')->on('users')->onDelete('set null');
            $table->text('change_notes')->nullable(); // Optional notes about what changed
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('board_regulation_versions');
    }
};
