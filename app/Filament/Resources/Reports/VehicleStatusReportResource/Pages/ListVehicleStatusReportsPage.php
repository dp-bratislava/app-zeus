<?php

namespace App\Filament\Resources\Reports\VehicleStatusReportResource\Pages;

use App\Filament\Resources\Reports\VehicleStatusReportResource;
use App\Filament\Resources\Reports\VehicleStatusReportResource\Tables\VehicleStatusReportTable;
use Filament\Resources\Pages\Page;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Filament\Resources\Components\Tab;
use Illuminate\Database\Eloquent\Builder;

class ListVehicleStatusReportsPage extends Page implements HasTable
{
    use InteractsWithTable;

    protected static string $resource = VehicleStatusReportResource::class;

    protected static string $view = 'filament.resources.reports.vehicle-status-report.index';

    // public function vehicleStatusTable(Table $table): Table
    // {
    //     return VehicleStatusReportTable::make($table);
    // } 
    
    public function table(Table $table): Table
    {
        return VehicleStatusReportTable::make($table);
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
