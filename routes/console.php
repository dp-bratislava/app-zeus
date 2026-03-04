<?php

use Illuminate\Support\Facades\Schedule;

Schedule::command('datahub:update')->hourly();

Schedule::command('dpb-work-time-fund:sync-worktimes-for-all-departments')->everyTenMinutes();