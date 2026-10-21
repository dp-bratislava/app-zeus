<?php

namespace Dpb\Modules\Tasks\TaskBatches\Commands;

use Carbon\CarbonImmutable;
use Dpb\Modules\Tasks\TaskBatches\Context\TaskBatchHandlerContext;
use Dpb\Modules\Tasks\TaskBatches\DTO\TaskBatchTaskConfig;
use Illuminate\Support\Collection;

final readonly class ReconcileTaskItemsCommand
{
    /**
     * Summary of __construct
     * @param int $batchId
     * @param CarbonImmutable $date
     * @param Collection<TaskBatchTaskConfig> $taskConfigs
     * @param TaskBatchHandlerContext $context
     */
    public function __construct(
        public int $batchId,
        public CarbonImmutable $date,
        public Collection $taskConfigs,
        public TaskBatchHandlerContext $context,
    ) {}
}