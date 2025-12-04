<?php

namespace App\Filament\Resources\Reports\VehicleStatusReportResource\Tables;

use Dpb\Package\Fleet\Models\Vehicle;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Carbon;

class VehicleStatusReportTableFilters
{
    public static function make(): array
    {
        return [
            // model
            Tables\Filters\SelectFilter::make('vehicle.model')
                ->label(__('reports/vehicle-status-report.table.filters.model'))
                ->relationship('vehicle.model', 'title')
                ->searchable()
                ->multiple()
                ->preload(),
            // state
            // Tables\Filters\SelectFilter::make('state')
            //     ->label(__('reports/vehicle-status-report.table.columns.state'))
            //     ->searchable()
            //     ->multiple()
            //     ->preload()
            //     ->options([
            //         'in-service' => __('daily-expedition.states.in-service'),
            //         'split-service' => __('daily-expedition.states.split-service'),
            //         'out-of-service' => __('daily-expedition.states.out-of-service'),
            //     ]),
            // maintenance group
            // Tables\Filters\SelectFilter::make('vehicle.maintenanceGroup')
            //     ->label(__('reports/vehicle-status-report.table.columns.maintenance_group.label'))
            //     ->relationship('vehicle.maintenanceGroup', 'title')
            //     ->searchable()
            //     ->preload()
            //     ->multiple()
            //     // ->options(fn() => MaintenanceGroup::pluck('title')),
        ];
    }
}
