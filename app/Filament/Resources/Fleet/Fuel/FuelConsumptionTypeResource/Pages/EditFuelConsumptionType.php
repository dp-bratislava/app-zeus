<?php

namespace App\Filament\Resources\Fleet\Fuel\FuelConsumptionTypeResource\Pages;

use App\Filament\Resources\Fleet\Fuel\FuelConsumptionTypeResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditFuelConsumptionType extends EditRecord
{
    protected static string $resource = FuelConsumptionTypeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
