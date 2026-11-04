<?php

namespace Dpb\Modules\Tasks\Filament\Resources\TaskBatches\Mappers;

use Dpb\Modules\Tasks\Enums\TaskBatchMode;

class TaskBatchUpdateFormMapperResolver
{
    public function resolve(TaskBatchMode $mode): TaskBatchUpdateFormMapper
    {
        return match ($mode) {
            TaskBatchMode::Uniform => app(UniformTaskBatchUpdateFormMapper::class),
            TaskBatchMode::Diverse => app(DiverseTaskBatchUpdateFormMapper::class),
        };
    }
}