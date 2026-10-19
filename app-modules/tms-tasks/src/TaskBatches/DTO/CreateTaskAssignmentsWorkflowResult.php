<?php

namespace Dpb\Modules\Tasks\TaskBatches\DTO;

use Illuminate\Support\Collection;

final readonly class CreateTaskAssignmentsWorkflowResult
{
    /**
     * Summary of __construct
     * @param array $batchRecords
     * @param Collection<WtfTaskItemConfig> $taskItemConfigs // keyed by TaskItem uuid
     */
    public function __construct(
        public array $batchRecords,
        public Collection $taskItemConfigs,
    ) {}
}