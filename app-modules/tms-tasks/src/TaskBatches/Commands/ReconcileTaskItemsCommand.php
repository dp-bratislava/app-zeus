<?php

namespace Dpb\Modules\Tasks\TaskBatches\Commands;

use Carbon\CarbonImmutable;
use Dpb\Modules\Tasks\TaskBatches\Context\TaskBatchHandlerContext;

final readonly class ReconcileTaskItemsCommand
{
    public function __construct(
        public int $batchId,
        public CarbonImmutable $date,
        public array $taskIds,
        public array $taskItemGroupIds,
        public TaskBatchHandlerContext $context,
    ) {}
}