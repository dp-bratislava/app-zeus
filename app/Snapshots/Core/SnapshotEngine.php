<?php

namespace App\Snapshots\Core;

use App\Snapshots\Core\Contracts\SnapshotContract;

class SnapshotEngine
{
    public function __construct(
        protected TempTableManager $tempTables,
    ) {}

    public function run(SnapshotContract $snapshot, SnapshotRunContext $context): void
    {
        $strategy = $snapshot->strategy();

        $strategy->execute($snapshot, $context);
    }
}
