<?php

namespace Dpb\Modules\Tasks\TaskBatches\Commands;

use Carbon\CarbonImmutable;
use Dpb\Modules\Tasks\TaskBatches\Context\TaskBatchHandlerContext;
use Dpb\Modules\Tasks\TaskBatches\DTO\SubjectTaskConfig;

final readonly class CreateTaskDiverseBatchCommand
{
    /**
     * Summary of __construct
     * @param CarbonImmutable $date
     * @param list<SubjectTaskConfig> $configs
     * @param TaskBatchHandlerContext $context
     */
    public function __construct(
        public CarbonImmutable $date,
        public array $configs,
        public TaskBatchHandlerContext $context,
    ) {}
}