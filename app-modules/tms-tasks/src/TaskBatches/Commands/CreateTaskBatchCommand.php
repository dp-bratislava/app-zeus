<?php

namespace Dpb\Modules\Tasks\TaskBatches\Commands;

use Carbon\CarbonImmutable;
use Dpb\Modules\Tasks\TaskBatches\Context\TaskBatchHandlerContext;
use Dpb\Modules\Tasks\DTO\TaskSubjectReference;

final readonly class CreateTaskBatchCommand
{
    /**      
     * @param CarbonImmutable $date
     * @param int $taskGroupId
     * @param list<TaskSubjectReference> $subjects
     * @param array $taskItemGroupIds
     * @param TaskBatchHandlerContext $context
     */
    public function __construct(
        public CarbonImmutable $date,
        public int $taskGroupId,
        public array $subjects,
        public array $taskItemGroupIds,
        public TaskBatchHandlerContext $context,
    ) {}
}