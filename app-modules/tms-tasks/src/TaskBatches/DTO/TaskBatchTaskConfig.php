<?php

namespace Dpb\Modules\Tasks\TaskBatches\DTO;

final readonly class TaskBatchTaskConfig
{
    /**
     * @param int $taskGroupId
     * @param TaskSubjectReference $subject
     * @param list<TaskBatchTaskItemGroupConfig> $taskItemGroups
     * @param mixed $note
     */
    public function __construct(
        public int $taskGroupId,
        public TaskSubjectReference $subject,
        public array $taskItemGroups,
        public ?string $note = null,
    ) {}
}