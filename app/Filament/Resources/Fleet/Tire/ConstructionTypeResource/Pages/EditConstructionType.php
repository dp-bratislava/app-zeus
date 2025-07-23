<?php

namespace App\Filament\Resources\Fleet\Tire\ConstructionTypeResource\Pages;

use App\Filament\Resources\Fleet\Tire\ConstructionTypeResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditConstructionType extends EditRecord
{
    protected static string $resource = ConstructionTypeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
