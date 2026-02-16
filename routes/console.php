<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Schedule;

Schedule::command('users:check-idle')->everyMinute();
Schedule::command('announcements:publish-scheduled')->everyMinute();
Schedule::command('messages:send-unread-reminders')->hourly();
// Birthday greetings: auto-detect users (consec/user) with birth_date = today and send once per day at 8:00 AM
Schedule::command('birthdays:send-greetings')->dailyAt('08:00');
