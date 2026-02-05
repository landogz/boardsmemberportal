<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Stores "delete for me" for 1:1 chats: when user_id deletes the conversation with other_user_id,
     * that conversation is hidden only for user_id; the other user still sees it.
     */
    public function up(): void
    {
        Schema::create('deleted_single_chats', function (Blueprint $table) {
            $table->id();
            $table->uuid('user_id');
            $table->uuid('other_user_id');
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
        Schema::dropIfExists('deleted_single_chats');
    }
};
