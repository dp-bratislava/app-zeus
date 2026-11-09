<?php

namespace Dpb\Modules\Tasks\TaskBatches\DTO;

use Illuminate\Support\Collection;

/**
 * List of ids of created entites
 */
final readonly class CreateTaskItemsWorkflowResult
{
    /**
     * Summary of __construct
     * @param array $taskItemIds
     * @param array $taskItemAssignmentIds
     * @param Collection<WtfTaskItemConfig> $taskItemConfigs // keyed by TaskItem uuid
     */
    public function __construct(
        public array $taskItemIds,
        public array $taskItemAssignmentIds,
        public Collection $taskItemConfigs,
    ) {}
}