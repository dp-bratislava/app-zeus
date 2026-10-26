<?php

namespace App\Console\Commands;

use Dpb\WorkTimeFund\Models\WorkTime;
use Dpb\WorkTimeFund\Services\ActivityRecord\BreakActivityService;
use Illuminate\Console\Command;

/**
 * Set all operations as scalable for department 9486
 */
class CreateBreakActivityCommand extends Command
{
    protected $signature = '
        wtf:create-break-activities
        {date : date (Y-m-d)}
        {personal-ids* : Personal IDs}
    ';

    protected $description = 'Generate break activities for contract on specified day';

    public function handle(
        BreakActivityService $breakActivityService
    ): void {
        // generate breaks for specified contract and date
        $personalIds = $this->argument('personal-ids');
        $date = $this->argument('date');

        $worktimes = WorkTime::query()
            ->with('breakActivityRecords')
            ->whereIn(column: 'personal_id', values: $personalIds)
            ->where(column: 'date', operator: '=', value: $date)
            ->get();

        // dd($worktimes->toRawSql())   ;

        foreach ($worktimes as $worktime) {
            $breakActivityService->attachBreakActivitiesForWorktime(
                $worktime
            );
        }
    }
}
