<?php

namespace Dpb\Modules\Tasks\Filament\Resources\TaskBatches\Pages;

use Dpb\Modules\Tasks\Filament\Resources\TaskBatches\TaskBatchResource;
use Filament\Resources\Pages\CreateRecord;

class CreateTaskBatch extends CreateRecord
{
    protected static string $resource = TaskBatchResource::class;
}
