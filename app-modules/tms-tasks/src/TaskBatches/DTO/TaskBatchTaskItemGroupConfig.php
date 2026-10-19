<?php

namespace Dpb\Modules\Tasks\TaskBatches\DTO;

use Illuminate\Support\Collection;

final readonly class TaskBatchTaskItemGroupConfig
{
    /**
     * Summary of __construct
     * @param int $taskItemGroupId
     * @param Collection<WtfTaskItemWork> $work
     */
    public function __construct(
        public int $taskItemGroupId,
        public Collection $work,
    ) {}
}