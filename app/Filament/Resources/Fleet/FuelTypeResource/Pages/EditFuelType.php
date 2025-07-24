<?php

namespace App\Filament\Resources\Fleet\FuelTypeResource\Pages;

use App\Filament\Resources\Fleet\FuelTypeResource;
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
