<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Add optional media_url for YouTube / Vimeo (or other) links instead of uploaded file.
     */
    public function up(): void
    {
        Schema::table('banner_slides', function (Blueprint $table) {
            $table->string('media_url', 1000)->nullable()->after('media_type');
        });
    }

    public function down(): void
    {
        Schema::table('banner_slides', function (Blueprint $table) {
            $table->dropColumn('media_url');
        });
    }
};
