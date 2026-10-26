<?php

namespace Dpb\Modules\Tasks\Filament\Resources\TaskBatches\Pages;

use Dpb\Modules\Tasks\Filament\Resources\TaskBatches\TaskBatchResource;
use Filament\Resources\Pages\CreateRecord;

class CreateUniformTaskBatch extends CreateRecord
{
    protected static string $resource = TaskBatchResource::class;
protected string $view = 'filament.resources.task-batches.pages.create-uniform-task-batch';

  
}
