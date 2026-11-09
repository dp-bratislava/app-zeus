<?php

namespace App\DataMigrations\TaskBatch;

use App\DataMigrations\Contracts\DataMigration;
use Dpb\Modules\Tasks\TaskBatches\Enums\TaskBatchContext;
use Dpb\Package\Batchable\Models\BatchContext;

class BatchContextMigration implements DataMigration
{
    public function run(): void
    {
        BatchContext::upsert(
            [
                'code' => TaskBatchContext::VehicleCleaning->value,
                'description' => TaskBatchContext::VehicleCleaning->label()
            ],
            ['code']
        );
    }
}
