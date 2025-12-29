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
        Schema::create('referendum_votes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('referendum_id')->constrained('referendums')->onDelete('cascade');
            $table->uuid('user_id');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->enum('vote', ['accept', 'decline']);
            $table->timestamps();
            
            // Ensure one vote per user per referendum
            $table->unique(['referendum_id', 'user_id']);
            $table->index('referendum_id');
            $table->index('user_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('referendum_votes');
    }
};
