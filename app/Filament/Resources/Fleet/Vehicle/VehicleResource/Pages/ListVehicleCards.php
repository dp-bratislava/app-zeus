<?php

namespace App\Filament\Resources\Fleet\Vehicle\VehicleResource\Pages;

use App\Filament\Resources\Fleet\Vehicle\VehicleResource;
use Dpb\Package\Fleet\Models\Vehicle;
use Dpb\Package\Fleet\Models\VehicleModel;
use Filament\Actions;
use Filament\Resources\Components\Tab;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Livewire\Attributes\On;

class ListVehicleCards extends ListRecords
{
    protected static string $resource = VehicleResource::class;
    protected static string $view = 'filament.resources.fleet.vehicle.pages.list-vehicle-cards';
    // Make the records available to Blade
    public $vehicles;
    public $vehicleModels;
    public ?string $modelFilter = null;    

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    public function getTabs(): array
    {
        return [
            'all' => Tab::make('All customers'),
            'active' => Tab::make('Active customers')
                ->modifyQueryUsing(fn(Builder $query) => $query->where('active', true)),
            'inactive' => Tab::make('Inactive customers')
                ->modifyQueryUsing(fn(Builder $query) => $query->where('active', false)),
        ];
    }

    public function mount(): void
    {
        // Fetch all vehicles or apply any filter
        $this->vehicles = Vehicle::with('model')->get()->toArray();
        $this->vehicleModels = app(VehicleModel::class)
            ->byType(['A', 'E', 'T'])
            ->has('vehicles')
            ->get()
            ->keyBy('id');
            // ->pluck('title', 'id');
    }

    #[On('filter-requested')]
    public function filterByModelId(?int $modelId): void
    {
        $this->modelFilter = $modelId;
    }

    public function getVehiclesProperty()
    {
        dd($this->vehicles);
        return Vehicle::query()
            ->when($this->modelFilter, fn ($q) => $q->where('model_id', $this->modelFilter))
            ->get()
            ->toArray();
    }    
}
