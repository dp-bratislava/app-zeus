<?php

namespace App\Console\Commands;

use Dpb\WorkTimeFund\Models\ActivityRecord;
use Illuminate\Console\Command;

/**
 * Set all operations as scalable for department 9486
 */
class DeleteBreakActivityCommand extends Command
{
    protected $signature = '
        wtf:delete-break-activities
        {date : date (Y-m-d)}
        {personal-ids* : Personal IDs}
    ';

    protected $description = 'Delete break activities for contract on specified day';

    public function handle(): void
    {
        // delete breaks for specified contract and date
        $personalIds = $this->argument('personal-ids');
        $date = $this->argument('date');

        ActivityRecord::query()
            ->whereIn(column: 'personal_id', values: $personalIds)
            ->where(column: 'date', operator: '=', value: $date)
            ->where(column: 'type', operator: '=', value: 'B')
            ->delete();
    }
}
