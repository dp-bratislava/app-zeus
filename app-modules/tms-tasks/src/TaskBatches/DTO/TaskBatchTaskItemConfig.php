<?php

namespace Dpb\Modules\Tasks\TaskBatches\DTO;

use Illuminate\Support\Collection;

final readonly class TaskBatchTaskItemConfig
{
    /**
     * Summary of __construct
     * @param int $taskItemGroupId
     * @param array $operationIds
     * @param Collection<WtfTaskItemWork> $work
     */
    public function __construct(
        public int $taskItemGroupId,
        public array $operationIds,
        public Collection $work,
    ) {}
}