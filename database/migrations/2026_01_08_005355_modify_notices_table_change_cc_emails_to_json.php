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
        // First, convert existing comma-separated emails to JSON format
        $notices = DB::table('notices')->whereNotNull('cc_emails')->get();
        
        foreach ($notices as $notice) {
            if (!empty($notice->cc_emails)) {
                $emails = array_map('trim', explode(',', $notice->cc_emails));
                $ccData = [];
                foreach ($emails as $email) {
                    if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
                        $ccData[] = [
                            'email' => $email,
                            'name' => '',
                            'position' => '',
                            'agency' => '',
                        ];
                    }
                }
                DB::table('notices')
                    ->where('id', $notice->id)
                    ->update(['cc_emails' => json_encode($ccData)]);
            }
        }
        
        // Change column type to JSON
        Schema::table('notices', function (Blueprint $table) {
            $table->json('cc_emails')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Convert JSON back to comma-separated emails
        $notices = DB::table('notices')->whereNotNull('cc_emails')->get();
        
        foreach ($notices as $notice) {
            $ccData = json_decode($notice->cc_emails, true);
            if (is_array($ccData)) {
                $emails = array_column($ccData, 'email');
                $emailString = implode(', ', array_filter($emails));
                DB::table('notices')
                    ->where('id', $notice->id)
                    ->update(['cc_emails' => $emailString]);
            }
        }
        
        // Change column type back to text
        Schema::table('notices', function (Blueprint $table) {
            $table->text('cc_emails')->nullable()->change();
        });
    }
};
