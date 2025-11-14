<?php

namespace App\Filament\Resources\TS\TicketResource\Tables;

use App\Filament\Components\VehiclePicker;
use Dpb\Package\Fleet\Models\MaintenanceGroup;
use Dpb\Package\Fleet\Models\Vehicle;
use Dpb\Package\Tickets\Models\TicketSource;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\ToggleButtons;
use App\States;

class TicketTableFilters
{
    public static function make(): array
    {
        return [
            // date
            Tables\Filters\Filter::make('date')
                ->form([
                    DatePicker::make('date')
                        ->label(__('tickets/ticket.table.filters.date')),
                ])
                ->query(function (Builder $query, array $data): Builder {
                    return $query
                        ->when(
                            $data['date'],
                            fn(Builder $query, $date): Builder =>
                            $query->whereHas('ticket', function ($q) use ($date) {
                                $q->whereDate('date', '=', $date);
                            })
                        );
                }),

            // ticket state
            Tables\Filters\Filter::make('state')
                ->form([
                    ToggleButtons::make('state')
                        ->options([
                            States\TS\TicketItem\Created::$name => __('tickets/ticket.states.created'),
                            States\TS\TicketItem\Closed::$name => __('tickets/ticket.states.closed'),
                            States\TS\TicketItem\Cancelled::$name => __('tickets/ticket.states.cancelled'),
                            States\TS\TicketItem\InProgress::$name => __('tickets/ticket.states.in-progress'),
                            States\TS\TicketItem\AwaitingParts::$name => __('tickets/ticket-item.states.awaiting-parts'),
                        ])
                        ->multiple()
                        ->inline()
                        ->label(__('tickets/ticket.table.filters.state')),
                ])
                ->query(function (Builder $query, array $data): Builder {
                    return $query
                        ->when(
                            $data['state'],
                            fn(Builder $query, $state): Builder =>
                            $query->whereHas('ticket', function ($q) use ($state) {
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
                        ->label(__('tickets/ticket.table.filters.subject')),
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
            // source
            Tables\Filters\SelectFilter::make('ticket_source_id')
                ->relationship('ticket.source', 'title')
                ->searchable()
                ->preload()
                ->multiple()
                ->label(__('tickets/ticket.table.filters.source')),
            // maintenance group
            Tables\Filters\Filter::make('assignedTo')
                ->form([
                    ToggleButtons::make('assignedTo')
                        ->options(MaintenanceGroup::pluck('code', 'id'))
                        ->multiple()
                        ->inline()
                        ->label(__('tickets/ticket.table.filters.assigned_to')),
                ])
                ->query(function (Builder $query, array $data): Builder {
                    return $query
                        ->when(
                            $data['assignedTo'],
                            fn(Builder $query, $assignedTo): Builder =>
                            $query->whereMorphedTo(
                                'assignedTo',
                                app(MaintenanceGroup::class)->getMorphClass(),
                            )
                                ->whereIn('assigned_to_id', $assignedTo)
                        );
                }),
        ];
    }
}
