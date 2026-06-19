<?php

namespace App\Snapshots\Core;

use App\Snapshots\Core\Contracts\SnapshotContract;
use App\Snapshots\Core\Contracts\SnapshotExecutionStrategy;
use App\Snapshots\Core\SnapshotExecutionState;
use App\Snapshots\Core\SnapshotRunContext;
use Illuminate\Support\Facades\DB;

abstract class Snapshot implements SnapshotContract
{
    abstract public function getKey(): string;

    abstract public function targetTable(): string;

    abstract public function sourceTable(): string;

    abstract public function strategy(): SnapshotExecutionStrategy;

    public function idQuery(SnapshotRunContext $context)
    {
        return DB::table($this->sourceTable())
            ->where('updated_at', '>', $context->from)
            ->orWhere('deleted_at', '>', $context->from)
            ->select('id');
    }

    abstract public function run(SnapshotRunContext $context, SnapshotExecutionState $state): void;

    abstract protected function map(): array;

    protected function columns(): string
    {
        return implode(',', array_keys($this->map()));
    }

    protected function duplicateKeyUpdate(string $pkColumn): string
    {
        return collect(array_keys($this->map()))
            ->reject(fn($column) => $column === $pkColumn) // PK
            ->map(fn($column) => "{$column} = VALUES({$column})")
            ->implode(",\n");
    }

    protected function select(): string
    {
        return implode(",\n", $this->map());
    }
}
