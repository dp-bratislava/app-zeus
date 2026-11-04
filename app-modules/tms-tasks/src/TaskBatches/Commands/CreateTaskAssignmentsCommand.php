<?php

namespace Dpb\Modules\Tasks\TaskBatches\Commands;

use Carbon\CarbonImmutable;
use Dpb\Modules\Tasks\TaskBatches\Context\TaskBatchHandlerContext;
use Dpb\Modules\Tasks\TaskBatches\DTO\TaskBatchTaskConfig;
use Illuminate\Support\Collection;

final readonly class CreateTaskAssignmentsCommand
{
    /**
     * @param CarbonImmutable $date
     * @param int $taskGroupId
     * @param Collection<TaskBatchTaskConfig> $taskConfigs
     * @param TaskBatchHandlerContext $context
     */
    public function __construct(
        public CarbonImmutable $date,
        public int $taskGroupId,
        public Collection $taskConfigs,
        public TaskBatchHandlerContext $context,
    ) {}
}