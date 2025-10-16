<?php

namespace App\Filament\Resources\Reports\VehicleStatusReportResource\Pages;

use App\Filament\Resources\Reports\VehicleStatusReportResource;
use Filament\Actions;
use Filament\Resources\Components\Tab;
use Filament\Resources\Pages\ListRecords;
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


    public function getTabs(): array
    {
        return [
            'all' => Tab::make('all'),
            'active' => Tab::make('1TP')
                ->modifyQueryUsing(fn(Builder $query) => $query->whereLike('code_1', '1%')),
            'inactive' => Tab::make('2TP')
                ->modifyQueryUsing(fn(Builder $query) => $query->whereLike('code_1', '2%')),
        ];
    }    
}
