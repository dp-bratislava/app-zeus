<?php

namespace Dpb\Modules\Tasks\TaskBatches\DTO;

final readonly class CreateTaskAssignmentsWorkflowResult
{
    public function __construct(
        public array $batchRecords,
    ) {}
}