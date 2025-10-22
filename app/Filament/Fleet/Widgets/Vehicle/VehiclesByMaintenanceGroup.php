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
            ->query(
                VehicleByMaintenanceGroup::query()
            )
            ->columns([
                Tables\Columns\TextColumn::make('maintenance_group'),
                Tables\Columns\TextColumn::make('under-repair'),
                Tables\Columns\TextColumn::make('warranty-claim'),
                Tables\Columns\TextColumn::make('warranty-repair'),
                Tables\Columns\TextColumn::make('missing-parts'),
            ]);
    }
}
