<?php

namespace Dpb\Modules\Tasks\TaskBatches\DTO;

final readonly class WtfTaskItemConfig
{
    /**
     * @param TaskSubjectReference $subject
     * @param int $taskItemGroupId
     * @param array $taskItemIds
     * @param array $work
     */
    public function __construct(
        public TaskSubjectReference $subject,
        public int $taskItemGroupId,
        public array $taskItemIds,
        public array $work,
    ) {}
}