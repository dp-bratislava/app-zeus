<?php

namespace App\Reports\Filters;

use Dpb\DatahubSync\Models\Department;
use Dpb\Departments\Services\DepartmentService;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables;
use Filament\Forms\Components\DatePicker;

class DetailReportFilter
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
        ];
    }
}
