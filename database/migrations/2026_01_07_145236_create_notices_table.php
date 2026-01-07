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
        // Drop old notices table if it exists
        Schema::dropIfExists('notice_user_access');
        Schema::dropIfExists('notices');
        
        // Create new notices table
        Schema::create('notices', function (Blueprint $table) {
            $table->id();
            $table->enum('notice_type', ['Notice of Meeting', 'Agenda', 'Other Matters'])->default('Notice of Meeting');
            $table->string('title');
            $table->unsignedBigInteger('related_notice_id')->nullable(); // For Agenda type - links to a Notice of Meeting
            $table->enum('meeting_type', ['online', 'onsite', 'hybrid'])->default('onsite');
            $table->string('meeting_link')->nullable(); // For online/hybrid meetings
            $table->text('description')->nullable();
            $table->unsignedBigInteger('attachment_id')->nullable(); // Single file attachment
            $table->text('cc_emails')->nullable(); // Comma-separated email addresses for non-registered users
            $table->uuid('created_by');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('cascade');
            $table->timestamps();
        });
        
        // Add foreign key constraints after table is created
        Schema::table('notices', function (Blueprint $table) {
            $table->foreign('related_notice_id')->references('id')->on('notices')->onDelete('set null');
            $table->foreign('attachment_id')->references('id')->on('media_library')->onDelete('set null');
        });
        
        // Create notice_user_access pivot table
        Schema::create('notice_user_access', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('notice_id');
            $table->foreign('notice_id')->references('id')->on('notices')->onDelete('cascade');
            $table->uuid('user_id');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->timestamps();
            
            $table->unique(['notice_id', 'user_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notices');
    }
};
