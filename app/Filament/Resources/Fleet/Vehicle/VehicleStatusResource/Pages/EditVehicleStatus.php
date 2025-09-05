<?php

namespace App\Filament\Resources\Fleet\Vehicle\VehicleStatusResource\Pages;

use App\Filament\Resources\Fleet\Vehicle\VehicleStatusResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditVehicleStatus extends EditRecord
{
    protected static string $resource = VehicleStatusResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
