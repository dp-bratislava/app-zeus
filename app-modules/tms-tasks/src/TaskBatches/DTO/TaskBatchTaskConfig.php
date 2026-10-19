<?php

namespace Dpb\Modules\Tasks\TaskBatches\DTO;

final readonly class TaskBatchTaskConfig
{
    /**
     * @param int $taskGroupId
     * @param TaskSubjectReference $subject
     * @param list<TaskBatchTaskItemGroupConfig> $tigConfigs
     * @param mixed $note
     */
    public function __construct(
        public int $taskGroupId,
        public TaskSubjectReference $subject,
        public array $tigConfigs,
        public ?string $note = null,
    ) {}
}