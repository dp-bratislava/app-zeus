<?php

namespace App\Filament\Resources\Fleet\Vehicle\VehicleModelResource\Pages;

use App\Filament\Resources\Fleet\Vehicle\VehicleModelResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Contracts\Support\Htmlable;

class CreateVehicleModel extends CreateRecord
{
    protected static string $resource = VehicleModelResource::class;

    public function getTitle(): string | Htmlable
    {
        return __('fleet/vehicle-model.create_heading');
    }      
}
