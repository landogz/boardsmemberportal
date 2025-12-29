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
            // Add foreign key for group_id if it doesn't exist
            if (!Schema::hasColumn('chats', 'group_id')) {
                $table->unsignedBigInteger('group_id')->nullable()->after('receiver_id');
            }
            
            // Add foreign key constraint if group_chats table exists
            if (Schema::hasTable('group_chats')) {
                $table->foreign('group_id')->references('id')->on('group_chats')->onDelete('cascade');
            }
            
            // Make receiver_id nullable for group messages
            $table->uuid('receiver_id')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('chats', function (Blueprint $table) {
            $table->dropForeign(['group_id']);
            $table->dropColumn('group_id');
        });
    }
};

