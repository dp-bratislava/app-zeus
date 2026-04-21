<?php

namespace App\Console\Commands\Snapshots;

use App\Jobs\Snapshots\SyncHRContractSnapshotJob;
use Illuminate\Console\Command;

class SyncHRContractSnapshot extends Command
{
    protected $signature = 'snapshot:sync-hr-contract {--force}';

    protected $description = 'Sync HR contract snapshot';

    public function handle(): int
    {
        $this->info('Dispatching hr-contract sync...');

        dispatch(new SyncHRContractSnapshotJob(
            // force: $this->option('force')
        ));

        return Command::SUCCESS;
    }
}
