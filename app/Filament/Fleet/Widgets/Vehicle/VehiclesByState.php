<?php

namespace App\Filament\Fleet\Widgets\Vehicle;

use App\Models\ReadOnly\Fleet\VehicleByState;
use Dpb\Package\Fleet\Models\Vehicle;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;


class VehiclesByState extends BaseWidget
{
    public function table(Table $table): Table
    {
        return $table
            ->heading(__('fleet/dashboard.widgets.vehicles_by_state.table.heading'))
            ->query(
                VehicleByState::query()
            )
            ->columns([
                Tables\Columns\TextColumn::make('state')
                    ->label(__('fleet/dashboard.widgets.vehicles_by_state.table.columns.state'))
                    ->getStateUsing(fn ($record) => __('fleet/vehicle.states.' . $record->state)),
                Tables\Columns\TextColumn::make('total')
                    ->label(__('fleet/dashboard.widgets.vehicles_by_state.table.columns.total')),
            ]);
    }
}
