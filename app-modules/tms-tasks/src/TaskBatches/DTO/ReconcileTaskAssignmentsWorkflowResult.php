<?php

namespace Dpb\Modules\Tasks\TaskBatches\DTO;

final readonly class ReconcileTaskAssignmentsWorkflowResult
{
    public function __construct(
        public array $recordsToCreate,
        public array $recordsToDelete,
    ) {}
}