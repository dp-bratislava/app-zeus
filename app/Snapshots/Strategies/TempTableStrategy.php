<?php

namespace App\Snapshots\Strategies;

use App\Snapshots\Core\Contracts\SnapshotContract;
use App\Snapshots\Core\Contracts\SnapshotExecutionStrategy;
use App\Snapshots\Core\SnapshotExecutionState;
use App\Snapshots\Core\SnapshotRunContext;
use App\Snapshots\Core\TempTableManager;
use Illuminate\Support\Facades\DB;

class TempTableStrategy implements SnapshotExecutionStrategy
{
    public function __construct(
        protected TempTableManager $tempTables,
    ) {}

    public function execute(SnapshotContract $snapshot, SnapshotRunContext $context): void
    {
        $state = new SnapshotExecutionState(
            tempTable: 'tmp_snapshot_ids'
        );

        $this->tempTables->create($state->tempTable);

        try {
            // fill tmp table 
            DB::table('tmp_snapshot_ids')->insertUsing(['id'], $snapshot->idQuery($context));

            // run snapshot
            $snapshot->run($context, $state);
        } finally {
            $this->tempTables->drop($state->tempTable);
        }
    }
}