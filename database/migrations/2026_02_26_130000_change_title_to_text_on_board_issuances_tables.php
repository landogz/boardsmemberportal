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
        Schema::table('board_regulations', function (Blueprint $table) {
            $table->text('title')->change();
        });

        // Board resolutions table may not exist on all deployments (it was recreated in a later migration)
        if (Schema::hasTable('board_resolutions')) {
            Schema::table('board_resolutions', function (Blueprint $table) {
                $table->text('title')->change();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('board_regulations', function (Blueprint $table) {
            $table->string('title')->change();
        });

        if (Schema::hasTable('board_resolutions')) {
            Schema::table('board_resolutions', function (Blueprint $table) {
                $table->string('title')->change();
            });
        }
    }
};

