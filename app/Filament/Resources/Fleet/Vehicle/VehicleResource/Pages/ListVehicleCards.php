<?php

namespace App\Filament\Resources\Fleet\Vehicle\VehicleResource\Pages;

use App\Filament\Resources\Fleet\Vehicle\VehicleResource;
use Dpb\Package\Fleet\Models\Vehicle;
use Filament\Actions;
use Filament\Resources\Components\Tab;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Contracts\Database\Eloquent\Builder;

class ListVehicleCards extends ListRecords
{
    protected static string $resource = VehicleResource::class;

    protected static string $view = 'filament.resources.fleet.vehicle.vehicle-resources.pages.list-vehicle-cards';    

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
            ->modifyQueryUsing(fn (Builder $query) => $query->where('active', true)),
        'inactive' => Tab::make('Inactive customers')
            ->modifyQueryUsing(fn (Builder $query) => $query->where('active', false)),
    ];
}

     // Make the records available to Blade
    public $vehicles;

    public function mount(): void
    {
        // Fetch all vehicles or apply any filter
        $this->vehicles = Vehicle::with('model')->get()->toArray();
    }   
}
