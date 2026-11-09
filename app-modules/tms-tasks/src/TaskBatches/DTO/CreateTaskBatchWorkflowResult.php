<?php

namespace Dpb\Modules\Tasks\TaskBatches\DTO;

use Dpb\Modules\Tasks\TaskBatches\Models\TaskBatch;
use Dpb\Package\Batchable\Models\Batch;

/**
 * List of created entites
 */
final readonly class CreateTaskBatchWorkflowResult
{
    public function __construct(
        public ?Batch $batch = null,
        public ?TaskBatch $taskBatch = null,
    ) {}
}