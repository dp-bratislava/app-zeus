<?php

namespace Dpb\Modules\Tasks\TaskBatches\Commands;

use Carbon\CarbonImmutable;
use Dpb\Modules\Tasks\TaskBatches\Context\TaskBatchHandlerContext;

final readonly class CreateTaskItemsCommand
{
    public function __construct(
        public CarbonImmutable $date,
        public array $taskIds,
        public array $taskItemGroupIds,
        public TaskBatchHandlerContext $context,
    ) {}
}