<?php

namespace Dpb\Modules\Inspections\Filament\Resources\Inspections\UpcomingInspectionResource\Tables;

use Dpb\Package\Fleet\Models\Vehicle;
use Dpb\Modules\Inspections\Filament\Components\VehiclePicker;
use Filament\Forms\Components\DatePicker;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;

class UpcomingInspectionAssignmentTableFilters
{
    public static function make(): array
    {
        return [
            // date
            Tables\Filters\Filter::make('date')
                ->form([
                    DatePicker::make('date')
                        ->label(__('dpb-mod-inspections::upcoming-inspection.table.filters.date')),
                ])
                ->query(function (Builder $query, array $data): Builder {
                    return $query
                        ->when(
                            $data['date'],
                            fn (Builder $query, $date): Builder => $query->whereHas('inspection', function ($q) use ($date) {
                                $q->whereDate('date', '=', $date);
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
                                ->with(['codes' => fn ($q) => $q->orderByDesc('date_from'), 'model'])
                                ->get()
                                ->mapWithKeys(function (Vehicle $vehicle) {
                                    $latestCode = $vehicle->codes->first();
                                    if (! $latestCode) {
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
                        ->label(__('dpb-mod-inspections::upcoming-inspection.table.filters.subject')),
                ])
                ->query(function (Builder $query, array $data): Builder {
                    return $query
                        ->when(
                            $data['subject'],
                            fn (Builder $query, $subject): Builder => $query->whereMorphedTo(
                                'subject',
                                app(Vehicle::class)->getMorphClass(),
                            )
                                ->whereIn('subject_id', $subject)
                        );
                }),
        ];
    }
}
