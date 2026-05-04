<?php

namespace App\Snapshots\Core\Contracts;

use App\Snapshots\Core\SnapshotExecutionState;
use App\Snapshots\Core\SnapshotRunContext;


interface SnapshotContract
{
    public function getKey(): string;
    public function idQuery(SnapshotRunContext $context);

    public function run(SnapshotRunContext $context, SnapshotExecutionState $state): void;    

    public function strategy(): SnapshotExecutionStrategy;    
}