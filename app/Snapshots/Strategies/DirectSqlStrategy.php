<?php

namespace App\Snapshots\Strategies;

use App\Snapshots\Core\Contracts\SnapshotContract;
use App\Snapshots\Core\Contracts\SnapshotExecutionStrategy;
use App\Snapshots\Core\SnapshotExecutionState;
use App\Snapshots\Core\SnapshotRunContext;

class DirectSqlStrategy implements SnapshotExecutionStrategy
{
    public function execute(SnapshotContract $snapshot, SnapshotRunContext $context): void
    {
        $state = new SnapshotExecutionState(
            tempTable: ''
        );

        $snapshot->run($context, $state);
    }
}