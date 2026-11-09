<?php

namespace Dpb\Modules\Inspections\Filament\Resources\Inspections\InspectionTemplateResource\Pages;

use Dpb\Modules\Inspections\Filament\Resources\Inspections\InspectionTemplateResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Contracts\Support\Htmlable;

class ListInspectionTemplates extends ListRecords
{
    protected static string $resource = InspectionTemplateResource::class;

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
