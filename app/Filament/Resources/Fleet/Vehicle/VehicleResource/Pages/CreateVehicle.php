<?php

namespace App\Filament\Resources\Fleet\Vehicle\VehicleResource\Pages;

use App\Filament\Resources\Fleet\Vehicle\VehicleResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Contracts\Support\Htmlable;

class CreateVehicle extends CreateRecord
{
    protected static string $resource = VehicleResource::class;

    public function getTitle(): string | Htmlable
    {
        return __('fleet/vehicle.create_heading');
    }      
}
