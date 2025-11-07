<?php

namespace App\Filament\Resources\Inspection\UpcomingInspectionResource\Pages;

use App\Filament\Resources\Inspection\UpcomingInspectionResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListUpcomingInspections extends ListRecords
{
    protected static string $resource = UpcomingInspectionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
