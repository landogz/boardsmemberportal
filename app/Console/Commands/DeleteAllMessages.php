<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class DeleteAllMessages extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'messages:delete-all {--force : Skip confirmation}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Delete all messages (chats) and message reactions from the database';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        if (! $this->option('force')) {
            if (! $this->confirm('This will permanently delete ALL messages and reactions. Continue?')) {
                $this->info('Aborted.');
                return self::SUCCESS;
            }
        }

        $chatsCount = DB::table('chats')->count();
        $reactionsCount = 0;
        if (DB::getSchemaBuilder()->hasTable('message_reactions')) {
            $reactionsCount = DB::table('message_reactions')->count();
        }

        DB::statement('SET FOREIGN_KEY_CHECKS=0');

        try {
            if (DB::getSchemaBuilder()->hasTable('message_reactions')) {
                DB::table('message_reactions')->truncate();
                $this->info('Truncated message_reactions.');
            }
            DB::table('chats')->truncate();
            $this->info('Truncated chats.');
        } finally {
            DB::statement('SET FOREIGN_KEY_CHECKS=1');
        }

        $this->info("Deleted {$chatsCount} message(s) and {$reactionsCount} reaction(s).");
        return self::SUCCESS;
    }
}
