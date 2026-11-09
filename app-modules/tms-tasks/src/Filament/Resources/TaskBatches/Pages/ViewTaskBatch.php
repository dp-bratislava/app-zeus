<?php

namespace Dpb\Modules\Tasks\Filament\Resources\TaskBatches\Pages;

use App\Concerns\RedirectsOnDepartmentChange;
use Dpb\Modules\Tasks\Filament\Resources\TaskBatches\TaskBatchResource;
use Filament\Resources\Pages\ViewRecord;
use Illuminate\Contracts\Support\Htmlable;
use Override;

class ViewTaskBatch extends ViewRecord
{
    use RedirectsOnDepartmentChange;

    protected static string $resource = TaskBatchResource::class;

    #[Override]
    protected function canAccessAfterDepartmentChange(): bool
    {
        return static::getResource()::canView();
    }

    protected function getDepartmentChangeRedirectUrl(): string
    {
        return static::getResource()::getUrl('index');
    }

    public function getTitle(): string|Htmlable
    {
        return '';
    }
}
