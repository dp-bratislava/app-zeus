<?php

namespace App\Filament\Resources\Fleet\Vehicle\VehicleResource\Tables;

use App\Filament\Components\VehiclePicker;
use Dpb\Package\Fleet\Models\MaintenanceGroup;
use Dpb\Package\Fleet\Models\Vehicle;
use Dpb\Package\Tickets\Models\TicketSource;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\ToggleButtons;
use App\States;

class VehicleTableFilters
{
    public static function make(): array
    {
        return [
            // model
            Tables\Filters\SelectFilter::make('model')
                ->label(__('fleet/vehicle.table.columns.model'))
                ->relationship('model', 'title')
                ->searchable()
                ->multiple()
                ->preload(),
            // state
            Tables\Filters\SelectFilter::make('state')
                ->label(__('fleet/vehicle.table.columns.state'))
                ->searchable()
                ->multiple()
                ->preload()
                ->options([
                    States\Fleet\Vehicle\InService::$name => __('fleet/vehicle.states.in-service'),
                    States\Fleet\Vehicle\UnderRepair::$name => __('fleet/vehicle.states.under-repair'),
                    States\Fleet\Vehicle\MissingParts::$name => __('fleet/vehicle.states.missing-parts'),
                ]),
            // maintenance group
            Tables\Filters\SelectFilter::make('maintenanceGroup')
                ->label(__('fleet/vehicle.table.columns.maintenance_group.label'))
                ->relationship('maintenanceGroup', 'title')
                ->searchable()
                ->preload()
                ->multiple()
                ->options(fn() => MaintenanceGroup::pluck('title')),
        ];
    }
}
