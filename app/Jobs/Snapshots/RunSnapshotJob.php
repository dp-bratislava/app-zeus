<?php

namespace App\Jobs\Snapshots;

use App\Models\Snapshots\ReportSyncState;
use App\Snapshots\Core\SnapshotEngine;
use App\Snapshots\Core\SnapshotRegistry;
use App\Snapshots\Core\SnapshotRunContext;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class RunSnapshotJob implements ShouldQueue
{
    use Dispatchable, Queueable;

    public function __construct(
        public string $snapshotKey,
        public SnapshotRunContext $context
    ) {}

    public function handle(
        SnapshotRegistry $registry,
        SnapshotEngine $engine
    ): void {
        // resolve snapshot
        $snapshot = $registry->resolve($this->snapshotKey);

        // get effective from date based on provided filter options
        $effectiveFrom = $this->resolveFrom($snapshot);

        $context = new SnapshotRunContext(
            from: $effectiveFrom,
            to: $this->context->to,
            all: $this->context->all,
            force: $this->context->force,
        );

        // run snapshot update
        $engine->run($snapshot, $context);

        // store last update time for respective snapshot
        $syncState = ReportSyncState::byReportName($snapshot->getKey())->first();
        $syncState->last_synced_at = now()->toDateTimeString();
        $syncState->save();
    }

    private function resolveFrom($snapshot): string
    {
        // 1. Full sync ignores everything
        if ($this->context->all) {
            return '1970-01-01 00:00:00';
        }

        // 2. Explicit CLI override wins
        if ($this->context->from) {
            return $this->context->from;
        }

        // 3. Fallback to last sync
        return ReportSyncState::byReportName($snapshot->getKey())->first()?->last_synced_at
            ?? '1970-01-01 00:00:00';
    }
}
