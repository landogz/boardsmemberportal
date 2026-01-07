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
        Schema::table('notices', function (Blueprint $table) {
            // Drop the old foreign key constraint
            $table->dropForeign(['attachment_id']);
            // Drop the old column
            $table->dropColumn('attachment_id');
            // Add new JSON column for multiple attachments
            $table->json('attachments')->nullable()->after('description');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('notices', function (Blueprint $table) {
            // Drop the JSON column
            $table->dropColumn('attachments');
            // Add back the single attachment column
            $table->unsignedBigInteger('attachment_id')->nullable()->after('description');
            $table->foreign('attachment_id')->references('id')->on('media_library')->onDelete('set null');
        });
    }
};
