<?php

namespace App\Filament\Resources\Fleet\Vehicle\VehicleResource\Components;

use Dpb\Package\Fleet\Models\Vehicle;
use Livewire\Component;
class VehicleCard extends Component
{
    public array $vehicle;

    public function mount(Vehicle|array $vehicle): void
    {
        if ($vehicle instanceof Vehicle) {
            $vehicle->loadMissing('model');
        } else {
            $vehicle = Vehicle::with('model')->findOrFail($vehicle['id']);
        }

        $this->vehicle = [
            ...$vehicle->toArray(),
            'state' => $vehicle->getRawOriginal('state') ?? 'in-service',
            'code_1' => $vehicle->getCodeAttribute()?->code ?? 'N/A',
            'model' => $vehicle->model?->toArray(),
        ];
    }

    public function render()
    {
        return view('filament.resources.fleet.vehicle.components.card');
    }
}
