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
        Schema::table('chats', function (Blueprint $table) {
            // Drop the foreign key constraint first
            $table->dropForeign(['attachments']);
            // Change column from unsignedBigInteger to text to store JSON array
            $table->text('attachments')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('chats', function (Blueprint $table) {
            // Change back to unsignedBigInteger
            $table->unsignedBigInteger('attachments')->nullable()->change();
            // Re-add foreign key
            $table->foreign('attachments')->references('id')->on('media_library')->onDelete('set null');
        });
    }
};
