<?php

namespace Dpb\Modules\Tasks\TaskBatches\Commands;

use Carbon\CarbonImmutable;
use Dpb\Modules\Tasks\TaskBatches\Context\TaskBatchHandlerContext;
use Dpb\Modules\Tasks\TaskBatches\DTO\SubjectTaskConfig;

final readonly class CreateTaskBatchCommand
{
    /**
     * Summary of __construct
     * @param CarbonImmutable $date
     * @param int $taskGroupId
     * @param array $configuration
     * @param TaskBatchHandlerContext $context
     */
    public function __construct(
        public CarbonImmutable $date,
        public int $taskGroupId,
        public array $configuration,
        public TaskBatchHandlerContext $context,
    ) {}
}