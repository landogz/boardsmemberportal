<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('official_documents', function (Blueprint $table) {
            $table->date('approved_date')->nullable()->after('effective_date');
        });

        Schema::table('official_document_versions', function (Blueprint $table) {
            $table->date('approved_date')->nullable()->after('effective_date');
        });
    }

    public function down(): void
    {
        Schema::table('official_document_versions', function (Blueprint $table) {
            $table->dropColumn('approved_date');
        });

        Schema::table('official_documents', function (Blueprint $table) {
            $table->dropColumn('approved_date');
        });
    }
};

