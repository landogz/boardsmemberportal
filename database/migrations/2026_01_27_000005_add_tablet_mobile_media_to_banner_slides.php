<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('banner_slides', function (Blueprint $table) {
            $table->unsignedBigInteger('media_id_tablet')->nullable()->after('media_id');
            $table->unsignedBigInteger('media_id_mobile')->nullable()->after('media_id_tablet');

            $table->foreign('media_id_tablet')->references('id')->on('media_library')->onDelete('set null');
            $table->foreign('media_id_mobile')->references('id')->on('media_library')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('banner_slides', function (Blueprint $table) {
            $table->dropForeign(['media_id_tablet']);
            $table->dropForeign(['media_id_mobile']);
            $table->dropColumn(['media_id_tablet', 'media_id_mobile']);
        });
    }
};
