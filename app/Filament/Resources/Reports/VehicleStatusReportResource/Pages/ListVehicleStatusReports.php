<?php

namespace App\Filament\Resources\Reports\VehicleStatusReportResource\Pages;

use App\Filament\Resources\Reports\VehicleStatusReportResource;
use Dpb\Package\Fleet\Models\MaintenanceGroup;
use Filament\Actions;
use Filament\Resources\Components\Tab;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Builder;

class ListVehicleStatusReports extends ListRecords
{
    protected static string $resource = VehicleStatusReportResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // Actions\CreateAction::make(),
        ];
    }

    public function getTitle(): string | Htmlable
    {
        return '';
    }      

    public function getTabs(): array
    {
        $tabs = [];

        // Default “all” tab
        $tabs['all'] = Tab::make('Všetky');

        // Dynamic tabs
        foreach (MaintenanceGroup::get() as $group) {
            $tabs[$group->code] = Tab::make($group->title)
                ->label($group->code)
                ->modifyQueryUsing(
                    function(Builder $query) use ($group) {
                        $query->whereHas('vehicle', function($q) use ($group) {
                            $q->byMaintenanceGroup($group->code);
                        });
                    }
                );
        }

        return $tabs;
    }  
}
