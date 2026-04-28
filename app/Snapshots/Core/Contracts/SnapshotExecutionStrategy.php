<?php

namespace App\Snapshots\Core\Contracts;

use App\Snapshots\Core\SnapshotRunContext;


interface SnapshotExecutionStrategy
{
    public function execute(SnapshotContract $snapshot, SnapshotRunContext $context): void;    
}