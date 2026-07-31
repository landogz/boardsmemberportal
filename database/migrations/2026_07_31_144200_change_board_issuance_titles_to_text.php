<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Allow long board regulation / resolution titles (open text).
     * official_documents stores board resolutions; board_regulations may already be TEXT.
     */
    public function up(): void
    {
        $tables = [
            'official_documents' => false,
            'official_document_versions' => false,
            'board_regulations' => false,
            'board_regulation_versions' => false,
        ];

        if (Schema::hasTable('board_resolutions')) {
            $tables['board_resolutions'] = false;
        }

        foreach ($tables as $table => $_) {
            if (!Schema::hasTable($table) || !Schema::hasColumn($table, 'title')) {
                continue;
            }

            // TEXT NOT NULL for primary title; versions always have a snapshot title
            DB::statement("ALTER TABLE `{$table}` MODIFY `title` TEXT NOT NULL");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $tables = [
            'official_documents',
            'official_document_versions',
            'board_regulations',
            'board_regulation_versions',
        ];

        if (Schema::hasTable('board_resolutions')) {
            $tables[] = 'board_resolutions';
        }

        foreach ($tables as $table) {
            if (!Schema::hasTable($table) || !Schema::hasColumn($table, 'title')) {
                continue;
            }

            DB::statement("ALTER TABLE `{$table}` MODIFY `title` VARCHAR(255) NOT NULL");
        }
    }
};
