<?php

namespace Dpb\Modules\Tasks\Filament\Resources\TaskBatches\Mappers;

use Dpb\Modules\Tasks\TaskBatches\Commands\UpdateTaskBatchCommand;
use Dpb\Modules\Tasks\TaskBatches\Models\TaskBatch;

interface TaskBatchUpdateFormMapper
{
    public function fromForm(TaskBatch $record, array $data): UpdateTaskBatchCommand;

    public function fromDatabase(TaskBatch $record): array;
}
