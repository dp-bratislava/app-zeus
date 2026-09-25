<?php

namespace Dpb\Modules\Tasks\TaskBatches\DTO;

/**
 * List of ids of created entites
 */
final readonly class CreateTaskItemsWorkflowResult
{
    public function __construct(
        public array $taskItemIds,
        public array $taskItemAssignmentIds,
    ) {}
}