<?php

namespace Dpb\Modules\Tasks\TaskBatches\Commands;

use Carbon\CarbonImmutable;
use Dpb\Modules\Tasks\DTO\EntityIdMap;
use Dpb\Modules\Tasks\TaskBatches\Context\TaskBatchHandlerContext;
use Dpb\Modules\Tasks\TaskBatches\DTO\TaskBatchTaskConfig;
use Illuminate\Support\Collection;

final readonly class CreateTaskItemsCommand
{
    /**
     * Summary of __construct
     * @param CarbonImmutable $date
     * @param EntityIdMap $taskIdMap
     * @param Collection<TaskBatchTaskConfig> $taskConfigs // collection keyed by task uuid
     * @param TaskBatchHandlerContext $context
     */
    public function __construct(
        public CarbonImmutable $date,
        public EntityIdMap $taskIdMap,
        public Collection $taskConfigs,
        public TaskBatchHandlerContext $context,
    ) {}
}