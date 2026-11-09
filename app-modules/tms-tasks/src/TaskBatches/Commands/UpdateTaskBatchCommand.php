<?php

namespace Dpb\Modules\Tasks\TaskBatches\Commands;

use Carbon\CarbonImmutable;
use Dpb\Modules\Tasks\TaskBatches\Context\TaskBatchHandlerContext;

final readonly class UpdateTaskBatchCommand
{
    /**
     * Summary of __construct
     * @param int $taskBatchId
     * @param CarbonImmutable $date
     * @param array $configuration
     * @param TaskBatchHandlerContext $context
     */
    public function __construct(
        public int $taskBatchId,
        public CarbonImmutable $date,
        public array $configuration,
        public TaskBatchHandlerContext $context,
    ) {}
}