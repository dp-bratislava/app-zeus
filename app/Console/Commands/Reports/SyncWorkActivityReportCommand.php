<?php

namespace App\Console\Commands\Reports;

use App\Jobs\Reports\SyncWorkActivityReportJob;
use Illuminate\Console\Command;

class SyncWorkActivityReportCommand extends Command
{
    protected $signature = 'report:sync-work-activity {--force}';

    protected $description = 'Sync work activity report';

    public function handle(): int
    {
        $this->info('Dispatching work-activity-report sync...');

        dispatch(new SyncWorkActivityReportJob(
            // force: $this->option('force')
        ));

        return Command::SUCCESS;
    }
}
