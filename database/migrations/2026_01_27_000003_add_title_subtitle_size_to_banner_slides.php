<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('banner_slides', function (Blueprint $table) {
            $table->string('title_font_size', 20)->nullable()->after('title_color');
            $table->string('subtitle_font_size', 20)->nullable()->after('subtitle_color');
        });
    }

    public function down(): void
    {
        Schema::table('banner_slides', function (Blueprint $table) {
            $table->dropColumn(['title_font_size', 'subtitle_font_size']);
        });
    }
};
