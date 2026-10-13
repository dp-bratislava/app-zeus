<?php

namespace Dpb\Modules\Tasks\TaskBatches\Commands;

use Carbon\CarbonImmutable;
use Dpb\Modules\Tasks\DTO\EntityIdMap;
use Dpb\Modules\Tasks\TaskBatches\Context\TaskBatchHandlerContext;
use Illuminate\Support\Collection;

final readonly class CreateTaskItemsCommand
{
    public function __construct(
        public CarbonImmutable $date,
        public EntityIdMap $taskIdMap,
        public Collection $configs,
        public TaskBatchHandlerContext $context,
    ) {}
}