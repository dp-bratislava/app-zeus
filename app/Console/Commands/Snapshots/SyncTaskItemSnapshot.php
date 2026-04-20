<?php

namespace App\Console\Commands\Snapshots;

use App\Jobs\Snapshots\SyncTaskItemSnapshotJob;
use Illuminate\Console\Command;

class SyncTaskItemSnapshot extends Command
{
    protected $signature = 'snapshot:sync-task-item {--force}';

    protected $description = 'Sync task item snapshot';

    public function handle(): int
    {
        $this->info('Dispatching task-item-snapshot sync...');

        dispatch(new SyncTaskItemSnapshotJob(
            // force: $this->option('force')
        ));

        return Command::SUCCESS;
    }
}
