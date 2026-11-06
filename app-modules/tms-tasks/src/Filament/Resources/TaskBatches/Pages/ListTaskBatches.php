<?php

namespace Dpb\Modules\Tasks\Filament\Resources\TaskBatches\Pages;

use App\Concerns\RedirectsOnDepartmentChange;
use Dpb\Modules\Tasks\Filament\Resources\TaskBatches\TaskBatchResource;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Contracts\Support\Htmlable;
use Override;

class ListTaskBatches extends ListRecords
{
    use RedirectsOnDepartmentChange;

    protected static string $resource = TaskBatchResource::class;

    #[Override]
    protected function canStillAccessPage(): bool
    {
        return static::getResource()::canViewAny();
    }

    public function getTitle(): string|Htmlable
    {
        return '';
    }
}
