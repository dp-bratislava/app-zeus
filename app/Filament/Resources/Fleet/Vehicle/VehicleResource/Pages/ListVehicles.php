<?php

namespace App\Filament\Resources\Fleet\Vehicle\VehicleResource\Pages;

use App\Filament\Resources\Fleet\Vehicle\VehicleResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Contracts\Support\Htmlable;

class ListVehicles extends ListRecords
{
    protected static string $resource = VehicleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // Actions\CreateAction::make(),
        ];
    }

    public function getTitle(): string | Htmlable
    {
        return '';
    }     
}
