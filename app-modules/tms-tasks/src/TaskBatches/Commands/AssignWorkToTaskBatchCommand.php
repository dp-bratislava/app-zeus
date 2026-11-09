<?php

namespace Dpb\Modules\Tasks\TaskBatches\Commands;

use Carbon\CarbonImmutable;
use Dpb\Modules\Tasks\TaskBatches\Context\TaskBatchHandlerContext;
use Dpb\Modules\Tasks\TaskBatches\DTO\WtfTaskItemConfig;
use Illuminate\Support\Collection;

final readonly class AssignWorkToTaskBatchCommand
{
    /**
     * Summary of __construct
     * @param int $batchId
     * @param int $taskBatchId
     * @param CarbonImmutable $date
     * @param Collection<WtfTaskItemConfig> $taskItemConfigs
     * @param TaskBatchHandlerContext $context
     */
    public function __construct(
        public int $batchId,
        public int $taskBatchId,
        public CarbonImmutable $date,
        public Collection $taskItemConfigs,
        public TaskBatchHandlerContext $context,        
    ) {}
}