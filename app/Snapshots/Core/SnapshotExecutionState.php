<?php

namespace App\Snapshots\Core;

class SnapshotExecutionState
{
    public function __construct(
        public string $tempTable,
    ) {}
}