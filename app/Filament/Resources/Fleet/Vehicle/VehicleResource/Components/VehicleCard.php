<?php

namespace App\Filament\Resources\Fleet\Vehicle\VehicleResource\Components;

use Dpb\Package\Fleet\Models\Vehicle;
use Livewire\Component;

class VehicleCard extends Component
{
    public $vehicle;

    public function mount(Vehicle $vehicle) 
    {
        $this->vehicle = $vehicle;
    }

    public function render()
    {
        return view('filament.resources.fleet.vehicle.components.card');
    }
}
