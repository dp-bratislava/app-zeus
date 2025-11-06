<?php

namespace App\Filament\Resources\Fleet\Vehicle\VehicleResource\Tables;

use App\Filament\Imports\Fleet\VehicleImporter;
use App\Services\Fleet\VehicleService;
use App\States;
use App\StateTransitions\Fleet\Vehicle\DiscardedToInService;
use App\StateTransitions\Fleet\Vehicle\InServiceToDiscarded;
use App\StateTransitions\Fleet\Vehicle\InServiceToUnderRepair;
use App\StateTransitions\Fleet\Vehicle\UnderRepairToInService;
use Dpb\Package\Fleet\Models\MaintenanceGroup;
use Dpb\Package\Fleet\Models\Vehicle;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class VehicleTable
{
    public static function make(Table $table): Table
    {
        return $table
            ->paginated([10, 25, 50, 100, 'all'])
            ->defaultPaginationPageOption(100)
            ->recordClasses(fn($record) => match ($record->state?->getValue()) {
                States\Fleet\Vehicle\InService::$name => 'bg-green-100',
                States\Fleet\Vehicle\UnderRepair::$name => 'bg-yellow-100',
                States\Fleet\Vehicle\Discarded::$name => 'bg-red-100',
                default => null,
            })
            ->columns([
                Tables\Columns\TextColumn::make('code.code')
                    ->label(__('fleet/vehicle.table.columns.code'))
                    ->searchable(query: function (Builder $query, string $search): Builder {
                        return $query
                            ->whereHas('codes', function($q) use ($search) {
                                $q->whereLike('code', "%$search%");
                            });
                    }),
                Tables\Columns\TextColumn::make('model.title')
                    ->label(__('fleet/vehicle.table.columns.model'))
                    ->searchable(),
                Tables\Columns\TextColumn::make('licencePlate')
                    ->label(__('fleet/vehicle.table.columns.licence_plate'))
                    ->searchable(query: function (Builder $query, string $search): Builder {
                        return $query
                            ->whereHas('licencePlates', function($q) use ($search) {
                                $q->whereLike('code', "%$search%");
                            });
                    }),                    
                // Tables\Columns\TextColumn::make('model.type.title')
                //     ->label(__('fleet/vehicle.table.columns.type')),
                // Tables\Columns\TextColumn::make('groups.title')
                //     ->label(__('fleet/vehicle.table.columns.groups')),
                Tables\Columns\TextColumn::make('department')
                    ->label(__('fleet/vehicle.table.columns.department'))
                    ->state(function (VehicleService $svc, $record) {
                        return $svc->getDepartment($record)?->code;
                    }),
                Tables\Columns\TextColumn::make('total_distance')
                    ->state(function($record, VehicleService $vehicleService) {
                        return round($vehicleService->getTotalDistanceTraveled($record), 2);
                    }),
                Tables\Columns\TextColumn::make('distance_since_inspection')
                    ->state(function($record, VehicleService $vehicleService) {
                        return round($vehicleService->getInspectionDistanceTraveled($record), 2);
                    }),

                Tables\Columns\TextColumn::make('state')
                    ->label(__('fleet/vehicle.table.columns.state'))
                    ->badge()
                    ->state(fn(Vehicle $record) => $record->state?->label())
                    ->action(
                        Action::make('select')
                            ->requiresConfirmation()
                            ->action(function (Vehicle $record): void {
                                // dd($record->state);
                                $record->state == 'in-service'
                                    ? $record->state->transition(new InServiceToUnderRepair($record, auth()->guard()->user()))
                                    : $record->state->transition(new UnderRepairToInService($record, auth()->guard()->user()));
                                // ? $record->state->transition(new InServiceToDiscarded($record, auth()->guard()->user()))
                                // : $record->state->transition(new DiscardedToInService($record, auth()->guard()->user()));
                            }),
                    ),
                Tables\Columns\TextColumn::make('maintenanceGroup.code')
                    ->label(__('fleet/vehicle.table.columns.maintenance_group.label'))
                    ->tooltip(__('fleet/vehicle.table.columns.maintenance_group.tooltip'))
                    ->badge(),
                    // ->color(fn ($record) => $record?->maintenanceGroup?->color),
                // Tables\Columns\TextColumn::make('dp')
                //     ->state('1DPA'),
                Tables\Columns\IconColumn::make('under_warranty')
                    ->label(__('fleet/vehicle.table.columns.under_warranty.label'))
                    ->tooltip(__('fleet/vehicle.table.columns.under_warranty.tooltip'))
                    ->boolean()
                    ->default(false)
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('model')
                    ->label(__('fleet/vehicle.table.columns.model'))
                    ->relationship('model', 'title')
                    ->searchable()
                    ->multiple()
                    ->preload(),
                Tables\Filters\SelectFilter::make('state')
                    ->label(__('fleet/vehicle.table.columns.state'))
                    ->searchable()
                    ->multiple()
                    ->preload()
                    ->options([
                        States\Fleet\Vehicle\InService::$name => 'V prevádzke',
                        States\Fleet\Vehicle\UnderRepair::$name => 'V oprave',
                    ]),
                Tables\Filters\SelectFilter::make('tp')
                    ->label(__('fleet/vehicle.table.columns.maintenance_group.label'))
                    ->searchable()
                    ->multiple()
                    ->options(fn() => MaintenanceGroup::pluck('title')),
            ])
            ->headerActions([])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->visible(auth()->user()->can('fleet.vehicle-model.update')),
                Tables\Actions\ViewAction::make()
                    ->visible(auth()->user()->can('fleet.vehicle-model.read')),
                Tables\Actions\DeleteAction::make()
                    ->visible(auth()->user()->can('fleet.vehicle-model.delete')),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->visible(auth()->user()->can('fleet.vehicle-model.bulk-delete')),
                ]),
            ]);
    }
}
