<?php

namespace Dpb\Modules\Tasks\TaskBatches\DTO;

final readonly class TaskBatchInfolistRow
{
    /**
     * @param list<string> $personnel
     */
    public function __construct(
        public string $subjectLabel,
        public string $subjectDescription,
        public string $taskItemGroup,
        public array $personnel,
        public string $note = '',
    ) {}

    public function toArray(): array
    {
        return [
            'subject_label' => $this->subjectLabel,
            'subject_description' => $this->subjectDescription,
            'task_item_group' => $this->taskItemGroup,
            'personnel' => $this->personnel,
            'note' => $this->note,
        ];
    }
}