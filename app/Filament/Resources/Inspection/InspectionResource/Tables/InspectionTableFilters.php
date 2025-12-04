<?php

namespace App\Filament\Resources\Inspection\InspectionResource\Tables;

use App\Filament\Components\VehiclePicker;
use Dpb\Package\Fleet\Models\MaintenanceGroup;
use Dpb\Package\Fleet\Models\Vehicle;
use Dpb\Package\Tickets\Models\TicketSource;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\ToggleButtons;
use App\States;

class InspectionTableFilters
{
    public static function make(): array
    {
        return [
            // date
            Tables\Filters\Filter::make('date')
                ->form([
                    DatePicker::make('date')
                        ->label(__('inspections/inspection.table.filters.date')),
                ])
                ->query(function (Builder $query, array $data): Builder {
                    return $query
                        ->when(
                            $data['date'],
                            fn(Builder $query, $date): Builder =>
                            $query->whereHas('inspection', function ($q) use ($date) {
                                $q->whereDate('date', '=', $date);
                            })
                        );
                }),

            // inspection state
            Tables\Filters\Filter::make('state')
                ->form([
                    ToggleButtons::make('state')
                        ->options([
                            States\Inspection\Upcoming::$name => __('inspections/inspection.states.upcoming'),
                            States\Inspection\Overdue::$name => __('inspections/inspection.states.overdue'),
                            States\Inspection\InProgress::$name => __('inspections/inspection.states.in-progress'),
                        ])
                        ->multiple()
                        ->inline()
                        ->label(__('inspections/inspection.table.filters.state')),
                ])
                ->query(function (Builder $query, array $data): Builder {
                    return $query
                        ->when(
                            $data['state'],
                            fn(Builder $query, $state): Builder =>
                            $query->whereHas('inspection', function ($q) use ($state) {
                                $q->whereIn('state', $state);
                            })
                        );
                }),
            // subject
            Tables\Filters\Filter::make('subject')
                ->form([
                    VehiclePicker::make('subject')
                        ->options(
                            Vehicle::query()
                                ->has('codes')
                                ->with(['codes' => fn($q) => $q->orderByDesc('date_from'), 'model'])
                                ->get()
                                ->mapWithKeys(function (Vehicle $vehicle) {
                                    $latestCode = $vehicle->codes->first();
                                    if (!$latestCode) {
                                        return []; // important: return empty array if no code
                                    }
                                    return [
                                        $vehicle->id => $latestCode->code,
                                    ];
                                })
                                ->toArray()
                        )
                        ->getSearchResultsUsing(null)
                        ->getOptionLabelFromRecordUsing(null)
                        ->searchable()
                        ->multiple()
                        ->label(__('inspections/inspection.table.filters.subject')),
                ])
                ->query(function (Builder $query, array $data): Builder {
                    return $query
                        ->when(
                            $data['subject'],
                            fn(Builder $query, $subject): Builder =>
                            $query->whereMorphedTo(
                                'subject',
                                app(Vehicle::class)->getMorphClass(),
                            )
                                ->whereIn('subject_id', $subject)
                        );
                }),
            // maintenance group
            // Tables\Filters\Filter::make('maintenanceGroup')
            //     ->form([
            //         ToggleButtons::make('maintenanceGroup')
            //             ->options(MaintenanceGroup::pluck('code', 'id'))
            //             ->multiple()
            //             ->inline()
            //             ->label(__('inspections/daily-maintenance.table.filters.maintenance_group')),
            //     ])
            //     ->query(function (Builder $query, array $data): Builder {
            //         return $query
            //             ->when(
            //                 $data['maintenanceGroup'],
            //                 fn(Builder $query, $maintenanceGroup): Builder =>
            //                 $query->whereMorphedTo(
            //                     'maintenanceGroup',
            //                     app(MaintenanceGroup::class)->getMorphClass(),
            //                 )
            //                     ->whereIn('assigned_to_id', $maintenanceGroup)
            //             );
            //     }),
        ];
    }
}
