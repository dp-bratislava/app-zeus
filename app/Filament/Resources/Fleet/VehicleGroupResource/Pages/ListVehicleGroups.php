<?php

namespace App\Filament\Resources\Fleet\VehicleGroupResource\Pages;

use App\Filament\Resources\Fleet\VehicleGroupResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListVehicleGroups extends ListRecords
{
    protected static string $resource = VehicleGroupResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
