<?php

namespace App\Console\Commands\Snapshots;

use App\Jobs\Snapshots\SyncTaskItemSnapshotJob;
use App\Jobs\Snapshots\SyncWorkTaskSubjectSnapshotJob;
use Illuminate\Console\Command;

class SyncWorkTaskSubjectSnapshot extends Command
{
    protected $signature = 'snapshot:sync-work-task-subject {--force}';

    protected $description = 'Sync work task subject snapshot';

    public function handle(): int
    {
        $this->info('Dispatching work-task-subject-snapshot sync...');

        dispatch(new SyncWorkTaskSubjectSnapshotJob(
            // force: $this->option('force')
        ));

        return Command::SUCCESS;
    }
}
