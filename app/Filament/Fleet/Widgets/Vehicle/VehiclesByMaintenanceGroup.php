<?php

namespace App\Filament\Fleet\Widgets\Vehicle;

use App\Models\ReadOnly\Fleet\VehicleByMaintenanceGroup;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class VehiclesByMaintenanceGroup extends BaseWidget
{
    public function table(Table $table): Table
    {
        return $table
            ->heading(__('fleet/dashboard.widgets.vehicles_by_maintenance_group.table.heading'))
            ->query(
                VehicleByMaintenanceGroup::query()
            )
            ->columns([
                Tables\Columns\TextColumn::make('maintenance_group'),
                Tables\Columns\TextColumn::make('under-repair')
                    ->label(__('fleet/dashboard.widgets.vehicles_by_maintenance_group.table.columns.maintenance_group')),
                Tables\Columns\TextColumn::make('warranty-claim')
                    ->label(__('fleet/vehicle.states.warranty-claim')),
                Tables\Columns\TextColumn::make('warranty-repair')
                    ->label(__('fleet/vehicle.states.warranty-repair')),
                Tables\Columns\TextColumn::make('missing-parts')
                    ->label(__('fleet/vehicle.states.missing-parts')),
            ]);
    }
}
