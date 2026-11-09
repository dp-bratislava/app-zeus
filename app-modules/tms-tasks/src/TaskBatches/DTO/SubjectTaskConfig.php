<?php

namespace Dpb\Modules\Tasks\TaskBatches\DTO;

use Dpb\Modules\Tasks\TaskBatches\Enums\TaskSubjectType;

final readonly class SubjectTaskConfig
{
    /**
     * Summary of __construct
     * @param TaskSubjectReference $subject
     * @param list<SubjectTaskItemGroup> $taskItemGroups
     * @param mixed $note
     */
    public function __construct(
        public TaskSubjectReference $subject,
        public array $taskItemGroups,
        public ?string $note = null,
    ) {}
}