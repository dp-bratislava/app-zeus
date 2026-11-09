<?php

namespace Dpb\Modules\Tasks\Filament\Resources\TaskBatches\Mappers;

use Dpb\Modules\Tasks\Enums\TaskBatchMode;

class TaskBatchCreateFormMapperResolver
{
    public function resolve(TaskBatchMode $mode): TaskBatchCreateFormMapper
    {
        return match ($mode) {
            TaskBatchMode::Uniform => app(UniformTaskBatchCreateFormMapper::class),
            TaskBatchMode::Diverse => app(DiverseTaskBatchCreateFormMapper::class),
        };
    }
}