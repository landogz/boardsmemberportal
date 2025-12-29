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
        Schema::create('referendum_comments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('referendum_id')->constrained('referendums')->onDelete('cascade');
            $table->uuid('user_id');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreignId('parent_id')->nullable()->constrained('referendum_comments')->onDelete('cascade');
            $table->text('content');
            $table->softDeletes();
            $table->timestamps();
            
            $table->index('referendum_id');
            $table->index('user_id');
            $table->index('parent_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('referendum_comments');
    }
};
