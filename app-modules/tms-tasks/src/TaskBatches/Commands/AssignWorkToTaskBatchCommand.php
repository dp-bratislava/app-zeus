<?php

namespace Dpb\Modules\Tasks\TaskBatches\Commands;

final readonly class AssignWorkToTaskBatchCommand
{
    public function __construct(
        public int $batchId,
        public int $taskBatchId,
    ) {}
}