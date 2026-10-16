<?php

namespace Dpb\Modules\Tasks\TaskBatches\DTO;

final readonly class TaskBatchTaskItemGroupConfig
{
    public function __construct(
        public int $taskItemGroupId,
        public array $work,
    ) {}
}