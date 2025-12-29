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
        // Check if announcements table exists, if so, modify it
        if (Schema::hasTable('announcements')) {
            Schema::table('announcements', function (Blueprint $table) {
                // Add missing columns if they don't exist
                if (!Schema::hasColumn('announcements', 'banner_image_id')) {
                    $columnAfter = Schema::hasColumn('announcements', 'description') ? 'description' : 'content';
                    $table->unsignedBigInteger('banner_image_id')->nullable()->after($columnAfter);
                    $table->foreign('banner_image_id')->references('id')->on('media_library')->onDelete('set null');
                }
                if (!Schema::hasColumn('announcements', 'status')) {
                    $table->enum('status', ['draft', 'published'])->default('published')->after('banner_image_id');
                }
                if (!Schema::hasColumn('announcements', 'scheduled_at')) {
                    $table->dateTime('scheduled_at')->nullable()->after('status');
                }
                if (!Schema::hasColumn('announcements', 'deleted_at')) {
                    $table->softDeletes();
                }
            });
        } else {
            Schema::create('announcements', function (Blueprint $table) {
                $table->id();
                $table->string('title');
                $table->text('description'); // Rich text content
                $table->unsignedBigInteger('banner_image_id')->nullable();
                $table->foreign('banner_image_id')->references('id')->on('media_library')->onDelete('set null');
                $table->uuid('created_by');
                $table->foreign('created_by')->references('id')->on('users')->onDelete('cascade');
                $table->enum('status', ['draft', 'published'])->default('published');
                $table->dateTime('scheduled_at')->nullable(); // For scheduled announcements
                $table->softDeletes();
                $table->timestamps();
                
                $table->index('created_by');
                $table->index('status');
                $table->index('scheduled_at');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Only drop if we created it (not if it existed before)
        if (Schema::hasTable('announcements') && !Schema::hasColumn('announcements', 'publish_date')) {
            Schema::dropIfExists('announcements');
        } else {
            // Remove added columns
            Schema::table('announcements', function (Blueprint $table) {
                if (Schema::hasColumn('announcements', 'banner_image_id')) {
                    $table->dropForeign(['banner_image_id']);
                    $table->dropColumn('banner_image_id');
                }
                if (Schema::hasColumn('announcements', 'status')) {
                    $table->dropColumn('status');
                }
                if (Schema::hasColumn('announcements', 'scheduled_at')) {
                    $table->dropColumn('scheduled_at');
                }
                if (Schema::hasColumn('announcements', 'deleted_at')) {
                    $table->dropSoftDeletes();
                }
            });
        }
    }
};
