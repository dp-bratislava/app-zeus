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
            ->query(
                VehicleByModel::query()
            )
            ->columns([
                Tables\Columns\TextColumn::make('model'),
                Tables\Columns\TextColumn::make('total'),
                Tables\Columns\TextColumn::make('under-repair'),
            ]);
    }
}
