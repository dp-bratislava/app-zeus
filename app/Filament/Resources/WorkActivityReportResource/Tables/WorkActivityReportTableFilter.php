<?php

namespace App\Filament\Resources\WorkActivityReportResource\Tables;

use Dpb\DatahubSync\Models\Department;
use Dpb\Departments\Services\DepartmentService;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables;
use Filament\Forms\Components\DatePicker;

class WorkActivityReportTableFilter
{
    public static function make(): array
    {
        return [
            // activity date
            Tables\Filters\Filter::make('activity_date')
                ->form([
                    DatePicker::make('activity_date_from')
                        ->label(__('reports/work-activity-report.table.filters.date_from')),
                    DatePicker::make('activity_date_to')
                        ->label(__('reports/work-activity-report.table.filters.date_to')),

                ])
                ->query(function (Builder $query, array $data): Builder {
                    return $query
                        ->when(
                            $data['activity_date_from'],
                            fn(Builder $query, $date): Builder => $query->whereDate('activity_date', '>=', $date)
                        )
                        ->when(
                            $data['activity_date_to'],
                            fn(Builder $query, $date): Builder => $query->whereDate('activity_date', '<=', $date)
                        );
                }),

            // department
            Tables\Filters\SelectFilter::make('department')
                ->label(__('reports/work-activity-report.table.filters.department'))
                ->options(fn(DepartmentService $departmentSvc) => Department::whereIn('id', $departmentSvc->getAvailableDepartments()->pluck('id'))->pluck('code', 'code'))
                ->multiple()
                ->attribute('department_code'),

            // is fulfilled
            Tables\Filters\SelectFilter::make('is_fulfilled_label')
                ->label(__('reports/work-activity-report.table.filters.activity_is_fulfilled'))
                ->options([
                    'Nevyhodnotené' => 'Nevyhodnotené',
                    'Nie' => 'Nie',
                    'Áno' => 'Áno',
                ])
                ->multiple()
                ->attribute('activity_is_fulfilled_label'),

            // personal id
            // Tables\Filters\SelectFilter::make('personal_id')
            //     ->label(__('reports/work-activity-report.table.filters.department'))
            //     ->options(fn(DepartmentService $departmentSvc) => Department::whereIn('id', $departmentSvc->getAvailableDepartments()->pluck('id'))->pluck('code', 'code'))
            //     ->multiple()
            //     ->attribute('personal_id'),                

            // Tables\Filters\Filter::make('activity_date')
            //     ->form([
            //         DatePicker::make('activity_date_from')
            //             ->label(__('tms-ui::tickets/ticket.table.filters.date')),
            //         DatePicker::make('activity_date_to')
            //             ->label(__('tms-ui::tickets/ticket.table.filters.date')),

            //     ])
            //     ->query(function (Builder $query, array $data): Builder {
            //         return $query
            //             ->when(
            //                 $data['activity_date_from'],
            //                 fn(Builder $query, $date): Builder => $query->whereDate('activity_date', '>=', $date)                            
            //             )
            //             ->when(
            //                 $data['activity_date_to'],
            //                 fn(Builder $query, $date): Builder => $query->whereDate('activity_date', '<=', $date)                            
            //             );

            //     }),                
            /*
            // ticket state
            Tables\Filters\Filter::make('state')
                ->form([
                    ToggleButtons::make('state')
                        ->options([
                            States\Ticket\Created::$name => __('tms-ui::tickets/ticket.states.created'),
                            States\Ticket\Closed::$name => __('tms-ui::tickets/ticket.states.closed'),
                        ])
                        ->multiple()
                        ->inline()
                        ->label(__('tms-ui::tickets/ticket.table.filters.state')),
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
                        ->label(__('tms-ui::tickets/ticket.table.filters.subject')),
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
                */
        ];
    }
}
