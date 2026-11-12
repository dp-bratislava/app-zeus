<?php

namespace App\DataMigrations\TaskBatch;

use App\DataMigrations\Contracts\DataMigration;
use App\Enums\BatchContext as EnumsBatchContext;
use Dpb\Package\Batchable\Models\BatchContext;

class BatchContextMigration implements DataMigration
{
    public function run(): void
    {
        BatchContext::upsert(
            [
                'code' => EnumsBatchContext::TaskBatch->value,
                'description' => EnumsBatchContext::TaskBatch->label()
            ],
            ['code']
        );
    }
}
