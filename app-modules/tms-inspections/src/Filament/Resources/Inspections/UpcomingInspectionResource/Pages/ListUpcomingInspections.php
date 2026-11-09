<?php

namespace Dpb\Modules\Inspections\Filament\Resources\Inspections\UpcomingInspectionResource\Pages;

use Dpb\Modules\Inspections\Filament\Resources\Inspections\UpcomingInspectionResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Contracts\Support\Htmlable;

class ListUpcomingInspections extends ListRecords
{
    protected static string $resource = UpcomingInspectionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    public function getTitle(): string|Htmlable
    {
        return '';
    }
}
