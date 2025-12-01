<?php

namespace App\Filament\Resources\Fleet\Vehicle\VehicleGroupResource\Pages;

use App\Filament\Resources\Fleet\Vehicle\VehicleGroupResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Contracts\Support\Htmlable;

class ListVehicleGroups extends ListRecords
{
    protected static string $resource = VehicleGroupResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    public function getTitle(): string | Htmlable
    {
        return '';
    }      
}
