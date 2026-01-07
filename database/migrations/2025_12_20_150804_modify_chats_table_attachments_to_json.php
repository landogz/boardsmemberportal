<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Drop the foreign key constraint first if it exists
        $foreignKeys = DB::select("SELECT CONSTRAINT_NAME FROM information_schema.KEY_COLUMN_USAGE WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'chats' AND COLUMN_NAME = 'attachments' AND CONSTRAINT_NAME LIKE '%foreign%'");
        if (!empty($foreignKeys)) {
            $constraintName = $foreignKeys[0]->CONSTRAINT_NAME;
            DB::statement("ALTER TABLE chats DROP FOREIGN KEY {$constraintName}");
        }
        
        // Drop any indexes on the attachments column
        $indexes = DB::select("SHOW INDEXES FROM chats WHERE Column_name = 'attachments'");
        foreach ($indexes as $index) {
            if ($index->Key_name !== 'PRIMARY') {
                DB::statement("ALTER TABLE chats DROP INDEX {$index->Key_name}");
            }
        }
        
        // Change column type
        DB::statement('ALTER TABLE chats MODIFY attachments TEXT NULL');
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
