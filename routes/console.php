<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Schedule;

Schedule::command('users:check-idle')->everyMinute();
Schedule::command('announcements:publish-scheduled')->everyMinute();
