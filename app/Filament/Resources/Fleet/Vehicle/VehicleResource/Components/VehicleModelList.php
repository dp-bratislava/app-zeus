<?php

namespace App\Filament\Resources\Fleet\Vehicle\VehicleResource\Components;

use Livewire\Component;

class VehicleModelList extends Component
{
    public $vehicleModels;

    public function mount($vehicleModels): void
    {
        $this->vehicleModels = $vehicleModels;
    }

    public function applyFilter(?int $modelId): void
    {
        $this->dispatch('filter-requested', modelId: $modelId);
    }

    public function render()
    {
        return view('filament.resources.fleet.vehicle.components.vehicle-model-list');
    }
}
