<?php

namespace Dpb\Modules\Tasks\Filament\Resources\TaskBatches\Pages;

use Dpb\Modules\Tasks\Filament\Resources\TaskBatches\TaskBatchResource;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Contracts\Support\Htmlable;

class ListTaskBatches extends ListRecords
{
    protected static string $resource = TaskBatchResource::class;

    public function getTitle(): string|Htmlable
    {
        return '';
    }    
}
