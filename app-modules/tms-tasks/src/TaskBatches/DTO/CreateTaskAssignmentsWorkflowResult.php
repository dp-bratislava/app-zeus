<?php

namespace Dpb\Modules\Tasks\TaskBatches\DTO;

use Dpb\Modules\Tasks\DTO\EntityIdMap;
use Illuminate\Support\Collection;

final readonly class CreateTaskAssignmentsWorkflowResult
{
    /**
     * Summary of __construct
     * @param array $batchRecords
     * @param Collection<TaskBatchTaskConfig> $taskConfigs // keyed by Task uuid
     */
    public function __construct(
        public array $batchRecords,
        public EntityIdMap $taskIdMap,
        public Collection $taskConfigs,
    ) {}
}