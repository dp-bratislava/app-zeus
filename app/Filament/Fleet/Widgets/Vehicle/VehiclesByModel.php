<?php

namespace App\Filament\Fleet\Widgets\Vehicle;

use App\Models\ReadOnly\Fleet\VehicleByModel;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class VehiclesByModel extends BaseWidget
{
    public function table(Table $table): Table
    {
        return $table
            ->heading(__('fleet/dashboard.widgets.vehicles_by_model.table.heading'))
            ->query(
                VehicleByModel::query()
            )
            ->columns([
                Tables\Columns\TextColumn::make('model')
                    ->label(__('fleet/dashboard.widgets.vehicles_by_model.table.columns.model')),
                Tables\Columns\TextColumn::make('total')
                    ->label(__('fleet/dashboard.widgets.vehicles_by_model.table.columns.total')),
                Tables\Columns\TextColumn::make('under-repair')
                    ->label(__('fleet/dashboard.widgets.vehicles_by_model.table.columns.state'))
            ]);
    }
}
