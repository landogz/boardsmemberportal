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
        Schema::table('media_library', function (Blueprint $table) {
            $table->string('title')->nullable()->after('file_name');
            $table->text('alt_text')->nullable()->after('title');
            $table->text('caption')->nullable()->after('alt_text');
            $table->text('description')->nullable()->after('caption');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('media_library', function (Blueprint $table) {
            $table->dropColumn(['title', 'alt_text', 'caption', 'description']);
        });
    }
};
