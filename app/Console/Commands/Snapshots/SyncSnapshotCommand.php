<?php

namespace App\Console\Commands\Snapshots;

use App\Console\Concerns\InteractsWithDateRangeOptions;
use App\Jobs\Snapshots\RunSnapshotJob;
use App\Snapshots\Core\SnapshotRunContext;
use Illuminate\Console\Command;

class SyncSnapshotCommand extends Command
{
    use InteractsWithDateRangeOptions;

    protected $signature = '
        snapshot:sync
        {snapshot : Snapshot key}
        {--from= : Start date (Y-m-d)}
        {--to= : End date (Y-m-d)}
        {--all : Sync all data (ignore last sync)}
        {--force : Force rebuild}
    ';

    protected $description = 'Sync snapshot';

    public function handle(): int
    {
        $key = $this->argument('snapshot');

        $this->info("Dispatching [$key] sync...");

        [$from, $to] = $this->resolveDateRange();
        $this->logDateRange($from, $to);

        dispatch(new RunSnapshotJob(
            $key,
            new SnapshotRunContext(
                from: $from?->toDateTimeString(),
                to: $to?->toDateTimeString(),
                all: $this->isFullSync(),
                force: $this->isForced()
            ),
        ));

        $this->info("Sync snapshot [$key] dispatched");

        return Command::SUCCESS;
    }
}
