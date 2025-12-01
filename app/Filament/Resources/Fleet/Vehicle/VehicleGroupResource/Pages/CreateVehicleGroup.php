<?php

namespace App\Filament\Resources\Fleet\Vehicle\VehicleGroupResource\Pages;

use App\Filament\Resources\Fleet\Vehicle\VehicleGroupResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Contracts\Support\Htmlable;

class CreateVehicleGroup extends CreateRecord
{
    protected static string $resource = VehicleGroupResource::class;

    public function getTitle(): string | Htmlable
    {
        return __('fleet/vehicle-group.create_heading');
    }     
}
