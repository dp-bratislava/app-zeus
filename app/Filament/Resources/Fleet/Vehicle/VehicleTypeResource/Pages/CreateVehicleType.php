<?php

namespace App\Filament\Resources\Fleet\Vehicle\VehicleTypeResource\Pages;

use App\Filament\Resources\Fleet\Vehicle\VehicleTypeResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Contracts\Support\Htmlable;

class CreateVehicleType extends CreateRecord
{
    protected static string $resource = VehicleTypeResource::class;

    public function getTitle(): string | Htmlable
    {
        return __('fleet/vehicle-type.create_heading');
    }     
}
