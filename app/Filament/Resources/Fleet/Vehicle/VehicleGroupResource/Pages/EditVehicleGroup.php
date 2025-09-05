<?php

namespace App\Filament\Resources\Fleet\Vehicle\VehicleGroupResource\Pages;

use App\Filament\Resources\Fleet\Vehicle\VehicleGroupResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditVehicleGroup extends EditRecord
{
    protected static string $resource = VehicleGroupResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
