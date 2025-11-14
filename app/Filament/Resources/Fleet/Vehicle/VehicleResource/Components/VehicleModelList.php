<?php

namespace App\Filament\Resources\Fleet\Vehicle\VehicleResource\Components;

use Dpb\Package\Fleet\Models\VehicleModel;
use Livewire\Component;

class VehicleModelList extends Component
{
    public $vehicleModels;

    public function mount($vehicleModels) 
    {
        // $this->vehicleModels = app(VehicleModel::class)::pluck('title', 'id');
        $this->vehicleModels = $vehicleModels;
    }

    public function applyFilter(?int $modelId)
    {
        $this->dispatch('filter-requested', modelId: $modelId);
    }

    public function render()
    {
        return view('filament.resources.fleet.vehicle.components.vehicle-model-list');
    }
}
