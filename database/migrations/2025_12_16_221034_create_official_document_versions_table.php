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
        Schema::create('official_document_versions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('official_document_id');
            $table->foreign('official_document_id')->references('id')->on('official_documents')->onDelete('cascade');
            $table->unsignedBigInteger('pdf_file')->nullable(); // media_id - old file
            $table->foreign('pdf_file')->references('id')->on('media_library')->onDelete('set null');
            $table->string('version')->nullable(); // Version at time of change
            $table->string('title'); // Snapshot of title
            $table->text('description')->nullable(); // Snapshot of description
            $table->date('effective_date')->nullable(); // Snapshot of effective_date
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
        Schema::dropIfExists('official_document_versions');
    }
};
