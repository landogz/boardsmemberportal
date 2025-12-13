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
        Schema::create('notices', function (Blueprint $table) {
            $table->id();
            $table->string('subject');
            $table->text('content');
            $table->unsignedBigInteger('template_id')->nullable();
            $table->uuid('sent_by')->nullable();
            $table->foreign('sent_by')->references('id')->on('users')->onDelete('set null');
            $table->date('sent_date');
            $table->boolean('is_bulk')->default(false);
            $table->json('attachments')->nullable(); // media_ids array
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notices');
    }
};

