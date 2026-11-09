<?php

namespace Dpb\Modules\Inspections\Filament\Resources\Inspections\InspectionTemplateGroupResource\Pages;

use Dpb\Modules\Inspections\Filament\Resources\Inspections\InspectionTemplateGroupResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Contracts\Support\Htmlable;

class ListInspectionTemplateGroups extends ListRecords
{
    protected static string $resource = InspectionTemplateGroupResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // Actions\CreateAction::make(),
        ];
    }

    public function getTitle(): string|Htmlable
    {
        return '';
    }
}
