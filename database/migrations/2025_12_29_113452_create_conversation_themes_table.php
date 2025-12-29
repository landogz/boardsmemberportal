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
        Schema::create('conversation_themes', function (Blueprint $table) {
            $table->id();
            $table->uuid('user1_id');
            $table->foreign('user1_id')->references('id')->on('users')->onDelete('cascade');
            $table->uuid('user2_id');
            $table->foreign('user2_id')->references('id')->on('users')->onDelete('cascade');
            $table->string('theme')->nullable();
            $table->timestamps();
            
            // Ensure one theme per conversation pair (order-independent)
            $table->unique(['user1_id', 'user2_id']);
            $table->index(['user1_id', 'user2_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('conversation_themes');
    }
};
