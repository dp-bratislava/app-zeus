<?php

namespace Dpb\Modules\Tasks\TaskBatches\Commands;

use Carbon\CarbonImmutable;
use Dpb\Modules\Tasks\TaskBatches\Context\TaskBatchHandlerContext;
use Dpb\Modules\Tasks\DTO\TaskSubjectReference;

final readonly class CreateTaskAssignmentsCommand
{
    /**
     * @param list<TaskSubjectReference> $subjects
     * @param list<int> $taskItemGroupIds
     */
    public function __construct(
        public CarbonImmutable $date,
        public int $taskGroupId,
        public array $subjects,
        public array $taskItemGroupIds,
        public TaskBatchHandlerContext $context,
    ) {}
}