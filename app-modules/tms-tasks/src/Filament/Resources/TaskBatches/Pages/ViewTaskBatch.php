<?php

namespace Dpb\Modules\Tasks\Filament\Resources\TaskBatches\Pages;

use Dpb\Modules\Tasks\Filament\Resources\TaskBatches\TaskBatchResource;
use Filament\Resources\Pages\ViewRecord;
use Illuminate\Contracts\Support\Htmlable;

class ViewTaskBatch extends ViewRecord
{
    protected static string $resource = TaskBatchResource::class;

    public function getTitle(): string|Htmlable
    {
        return '';
    }

}
