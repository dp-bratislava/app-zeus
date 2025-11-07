<?php

namespace App\Filament\Resources\Fleet\MaintenanceGroupResource\Pages;

use App\Filament\Resources\Fleet\MaintenanceGroupResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListMaintenanceGroups extends ListRecords
{
    protected static string $resource = MaintenanceGroupResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
