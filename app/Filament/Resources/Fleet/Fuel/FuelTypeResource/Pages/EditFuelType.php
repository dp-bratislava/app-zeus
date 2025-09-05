<?php

namespace App\Filament\Resources\Fleet\Fuel\FuelTypeResource\Pages;

use App\Filament\Resources\Fleet\Fuel\FuelTypeResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditFuelType extends EditRecord
{
    protected static string $resource = FuelTypeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
