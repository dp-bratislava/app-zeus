<?php

use Illuminate\Support\Facades\Schedule;
use Illuminate\Support\Facades\Bus;
use App\Jobs\Snapshots\RunSnapshotJob;
use App\Snapshots\Core\SnapshotRunContext;

Schedule::command('datahub:update')->hourly();

Schedule::command('dpb-work-time-fund:sync-worktimes-for-all-departments')->dailyAt('00:10');

Schedule::call(function () {
    Bus::chain([
        new RunSnapshotJob('hr-contract', new SnapshotRunContext()),
        new RunSnapshotJob('fleet-vehicle', new SnapshotRunContext()),
        new RunSnapshotJob('work-task-subject', new SnapshotRunContext()),
        new RunSnapshotJob('tms-task-item', new SnapshotRunContext()),
        new RunSnapshotJob('work-activity', new SnapshotRunContext()),
    ])->dispatch();
})->everyMinute();