<?php

namespace App\Filament\Resources\TS\TicketItemResource\Tables;

use App\Filament\Components\VehiclePicker;
use Dpb\Package\Fleet\Models\MaintenanceGroup;
use Dpb\Package\Fleet\Models\Vehicle;
use Dpb\Package\Tickets\Models\TicketSource;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\ToggleButtons;
use App\States;

class TicketItemAssignmentTableFilters
{
    public static function make(): array
    {
        return [
            // date
            self::dateFilter(),

            // ticket item state
            self::stateFilter(),
            // subject
            // self::subjectFilter(),
        ];
    }

    private static function dateFilter()
    {
        return Tables\Filters\Filter::make('date')
            ->form([
                DatePicker::make('date')
                    ->label(__('tickets/ticket-item.table.filters.date')),
            ])
            ->query(function (Builder $query, array $data): Builder {
                return $query
                    ->when(
                        $data['date'],
                        fn(Builder $query, $date): Builder =>
                        $query->whereHas('ticketItem', function ($q) use ($date) {
                            $q->whereDate('date', '=', $date);
                        })
                    );
            });
    }

    private static function stateFilter()
    {
        return Tables\Filters\Filter::make('state')
            ->form([
                ToggleButtons::make('state')
                    ->options([
                        States\TS\TicketItem\Closed::$name => __('tickets/ticket-item.states.closed'),
                        States\TS\TicketItem\Cancelled::$name => __('tickets/ticket-item.states.cancelled'),
                        States\TS\TicketItem\InProgress::$name => __('tickets/ticket-item.states.in-progress'),
                        States\TS\TicketItem\AwaitingParts::$name => __('tickets/ticket-item.states.awaiting-parts'),
                    ])
                    ->multiple()
                    ->inline()
                    ->label(__('tickets/ticket-item.table.filters.state')),
            ])
            ->query(function (Builder $query, array $data): Builder {
                return $query
                    ->when(
                        $data['state'],
                        fn(Builder $query, $state): Builder =>
                        $query->whereHas('ticketItem', function ($q) use ($state) {
                            $q->whereIn('state', $state);
                        })
                    );
            });
    }

    private static function subjectFilter()
    {
        return Tables\Filters\Filter::make('subject')
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
                    ->label(__('tickets/ticket-item.table.filters.subject')),
            ])
            ->query(function (Builder $query, array $data): Builder {
                return $query
                    ->when(
                        $data['subject'],
                        fn(Builder $query, $subject): Builder =>
                        $query->whereHas('ticketItem.ticket', function ($q) use ($subject) {
                            $q->whereMorphedTo(
                                'subject',
                                app(Vehicle::class)->getMorphClass(),
                            )
                                ->whereIn('subject_id', $subject);
                        })
                    );
            });
    }
}
