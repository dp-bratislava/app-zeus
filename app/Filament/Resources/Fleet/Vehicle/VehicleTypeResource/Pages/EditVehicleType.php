<?php

namespace App\Filament\Resources\Fleet\Vehicle\VehicleTypeResource\Pages;

use App\Filament\Resources\Fleet\Vehicle\VehicleTypeResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditVehicleType extends EditRecord
{
    protected static string $resource = VehicleTypeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
