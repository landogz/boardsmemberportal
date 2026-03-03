<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ResetPortalData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'portal:reset-data 
                            {--with-users : Also delete Board Member and CONSEC users}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Danger: truncate core portal data (announcements, notices, referendums, etc.) and optionally remove non-admin users.';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->warn('DANGER: This will wipe a lot of data from the portal.');
        if (! $this->confirm('Are you sure you want to continue?', false)) {
            $this->info('Aborted.');
            return self::SUCCESS;
        }

        DB::statement('SET FOREIGN_KEY_CHECKS = 0');

        // Attendance Confirmations
        DB::table('attendance_confirmations')->truncate();

        // Agenda Inclusion Requests
        DB::table('agenda_inclusion_requests')->truncate();

        // Reference Materials
        DB::table('reference_materials')->truncate();

        // Announcements
        DB::table('announcement_user_access')->truncate();
        DB::table('announcements')->truncate();

        // Notices
        DB::table('notice_user_access')->truncate();
        DB::table('notices')->truncate();

        // Referendums
        DB::table('referendum_votes')->truncate();
        DB::table('referendum_comments')->truncate();
        DB::table('referendum_user_access')->truncate();
        DB::table('referendums')->truncate();

        // Banner
        DB::table('banner_slides')->truncate();

        // Chat / messaging (direct + group chats)
        // Truncate reactions first (FKs), then messages/chats and group metadata
        if (DB::getSchemaBuilder()->hasTable('message_reactions')) {
            DB::table('message_reactions')->truncate();
        }
        if (DB::getSchemaBuilder()->hasTable('message_attachments')) {
            DB::table('message_attachments')->truncate();
        }
        if (DB::getSchemaBuilder()->hasTable('chats')) {
            DB::table('chats')->truncate();
        }
        if (DB::getSchemaBuilder()->hasTable('group_chat_members')) {
            DB::table('group_chat_members')->truncate();
        }
        if (DB::getSchemaBuilder()->hasTable('group_chats')) {
            DB::table('group_chats')->truncate();
        }

        // Optional: remove Board Member & CONSEC users
        if ($this->option('with-users')) {
            $this->warn('Also deleting users with privilege = user / consec.');
            DB::table('users')
                ->whereIn('privilege', ['user', 'consec'])
                ->delete();
        }

        DB::statement('SET FOREIGN_KEY_CHECKS = 1');

        $this->info('Portal data reset completed successfully.');

        return self::SUCCESS;
    }
}

