<?php

namespace Dpb\Modules\Tasks\Filament\Resources\TaskBatches\Mappers;

use Dpb\Modules\Tasks\TaskBatches\Commands\CreateTaskBatchCommand;

interface TaskBatchCreateFormMapper
{
    public function fromForm(array $data): CreateTaskBatchCommand;
}
