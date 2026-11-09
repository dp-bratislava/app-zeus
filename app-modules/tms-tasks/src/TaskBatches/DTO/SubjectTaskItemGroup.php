<?php

namespace Dpb\Modules\Tasks\TaskBatches\DTO;

final readonly class SubjectTaskItemGroup
{
    public function __construct(
        public int|string $taskItemGroupId,
        public int $quantity = 1,
    ) {}
}