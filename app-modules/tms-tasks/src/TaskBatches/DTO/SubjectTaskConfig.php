<?php

namespace Dpb\Modules\Tasks\TaskBatches\DTO;

use Dpb\Modules\Tasks\TaskBatches\Enums\TaskSubjectType;

final readonly class SubjectTaskConfig
{
    /**
     * Summary of __construct
     * @param TaskSubjectType $subjectType
     * @param int|string $subjectId
     * @param list<SubjectTaskItemGroup> $taskItemGroups
     * @param mixed $note
     */
    public function __construct(
        public TaskSubjectType $subjectType,
        public int|string $subjectId,
        public array $taskItemGroups,
        public ?string $note,
    ) {}
}