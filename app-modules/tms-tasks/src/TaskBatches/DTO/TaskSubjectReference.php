<?php

namespace Dpb\Modules\Tasks\TaskBatches\DTO;

use Dpb\Modules\Tasks\TaskBatches\Enums\TaskSubjectType as EnumsTaskSubjectType;

final readonly class TaskSubjectReference
{
    public function __construct(
        public EnumsTaskSubjectType $type,
        public int|string $id,
    ) {}
}