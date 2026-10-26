<?php

namespace Dpb\Modules\Tasks\TaskBatches\DTO;

use Illuminate\Support\Collection;

final readonly class WtfTaskItemConfig
{
    /**
     * @param TaskSubjectReference $subject
     * @param int $taskItemGroupId
     * @param int $taskItemId
     * @param Collection<WtfTaskItemWork> $work
     * @param array $operationIds
     */
    public function __construct(
        public TaskSubjectReference $subject,
        public int $taskItemGroupId,
        public int $taskItemId,
        public Collection $work,
        public array $operationIds,
    ) {}
}