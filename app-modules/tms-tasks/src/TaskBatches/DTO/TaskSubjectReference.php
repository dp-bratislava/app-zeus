<?php

namespace Dpb\Modules\Tasks\TaskBatches\DTO;

use Dpb\Modules\Tasks\TaskBatches\Enums\TaskSubjectType;

final readonly class TaskSubjectReference
{
    public function __construct(
        public TaskSubjectType $type,
        public int|string $id,
    ) {}
}