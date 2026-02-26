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
        Schema::table('official_documents', function (Blueprint $table) {
            $table->unsignedBigInteger('notice_id')->nullable()->after('uploaded_by');
            $table->foreign('notice_id')->references('id')->on('notices')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('official_documents', function (Blueprint $table) {
            $table->dropForeign(['notice_id']);
            $table->dropColumn('notice_id');
        });
    }
};
