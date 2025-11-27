<?php

namespace App\Filament\Resources\Inspection\UpcomingInspectionResource\Tables;

use App\Filament\Components\VehiclePicker;
use Dpb\Package\Fleet\Models\Vehicle;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables;
use Filament\Forms\Components\DatePicker;

class UpcomingInspectionAssignmentTableFilters
{
    public static function make(): array
    {
        return [
            // date
            Tables\Filters\Filter::make('date')
                ->form([
                    DatePicker::make('date')
                        ->label(__('inspections/upcoming-inspection.table.filters.date')),
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
                        ->label(__('inspections/upcoming-inspection.table.filters.subject')),
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
        ];
    }
}
