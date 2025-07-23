<?php

namespace App\Filament\Resources\Fleet\Tire\ConstructionTypeResource\Pages;

use App\Filament\Resources\Fleet\Tire\ConstructionTypeResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListConstructionTypes extends ListRecords
{
    protected static string $resource = ConstructionTypeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
