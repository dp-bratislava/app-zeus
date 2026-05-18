<?php

namespace App\Snapshots\Core;

use App\Snapshots\Core\Contracts\SnapshotContract;
use Illuminate\Support\Facades\DB;

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
