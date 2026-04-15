<?php

use Illuminate\Support\Facades\Schedule;

Schedule::command('datahub:update')->hourly();

Schedule::command('dpb-work-time-fund:sync-worktimes-for-all-departments')->dailyAt('00:10');
Schedule::command('report:sync-work-activity')->everyTenMinutes()->withoutOverlapping();