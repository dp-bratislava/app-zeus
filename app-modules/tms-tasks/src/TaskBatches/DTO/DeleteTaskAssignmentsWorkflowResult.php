<?php

namespace Dpb\Modules\Tasks\TaskBatches\DTO;

final readonly class DeleteTaskAssignmentsWorkflowResult
{
    public function __construct(
        public array $batchRecords,
    ) {}
}