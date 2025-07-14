<?php

namespace App\Filament\Resources\BM\BuildingResource\Pages;

use App\Filament\Resources\BM\BuildingResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditBuilding extends EditRecord
{
    protected static string $resource = BuildingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
