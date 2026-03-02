<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * When a user "deletes" a 1:1 conversation, we store cleared_at for that user only.
     * When loading messages for that user with that partner, only messages after cleared_at are shown.
     * The other user sees the full conversation (no row for them).
     */
    public function up(): void
    {
        Schema::create('conversation_cleared', function (Blueprint $table) {
            $table->id();
            $table->uuid('user_id');
            $table->uuid('other_user_id');
            $table->timestamp('cleared_at');
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('other_user_id')->references('id')->on('users')->onDelete('cascade');
            $table->unique(['user_id', 'other_user_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('conversation_cleared');
    }
};
