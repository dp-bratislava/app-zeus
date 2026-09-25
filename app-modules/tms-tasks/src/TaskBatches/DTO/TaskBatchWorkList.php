<?php

namespace Dpb\Modules\Tasks\TaskBatches\DTO;

use Illuminate\Support\Collection;

final readonly class TaskBatchWorkList
{
    public function __construct(
        public array $contractIds,
        public Collection $operations,
        public array $worktimeIds,
    ) {}
}