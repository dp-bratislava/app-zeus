<?php

use Illuminate\Support\Facades\Schedule;

Schedule::command('datahub:update')->hourly();

// turned off to enforce manual sync only for departments that want it
// Schedule::command('dpb-work-time-fund:sync-worktimes-for-all-departments')->dailyAt('00:10');