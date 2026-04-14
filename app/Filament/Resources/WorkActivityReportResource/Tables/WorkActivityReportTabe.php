<?php

namespace App\Filament\Resources\WorkActivityReportResource\Tables;

use App\Filament\Exports\Reports\WorkActivityReportExporter;
use App\Models\DispatchReport;
use App\Services\Fleet\VehicleService;
use App\Services\Inspection\CreateTicketService;
use App\Services\Inspection\AssignmentService as InspectionAssignmentService;
use App\Services\TS\TicketAssignmentService;
use App\States;
use Dpb\Departments\Services\DepartmentService;
use Dpb\Package\Inspections\Models\Inspection;
use Filament\Actions\Exports\Enums\ExportFormat;
use Filament\Actions\Exports\Models\Export;
use Filament\Tables;
use Filament\Tables\Actions\ExportAction;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

class WorkActivityReportTabe
{
    public static function make(Table $table): Table
    {
        return $table
            ->deferLoading()
            ->modifyQueryUsing(function (
                Builder $query,
                DepartmentService $departmentSvc
            ) {
                return $query
                    ->whereIn('department_id', $departmentSvc->getAvailableDepartments()->pluck('id'));
            })
            ->paginated([10, 25, 50, 100, 'all'])
            ->defaultPaginationPageOption(100)
            // ->recordClasses(fn($record) => match ($record->state?->getValue()) {
            //     States\Inspection\Upcoming::$name => 'bg-blue-200',
            //     States\Inspection\InProgress::$name => 'bg-yellow-200',
            //     default => null,
            // })
            ->columns([
                Tables\Columns\TextColumn::make('activity_date')->date('j.n.Y'),
                // ->label(__('dispatch-report.table.columns.date.label')),
                Tables\Columns\TextColumn::make('personal_id'),
                Tables\Columns\TextColumn::make('last_name'),
                Tables\Columns\TextColumn::make('department_code'),
                Tables\Columns\TextColumn::make('subject_code'),
                Tables\Columns\TextColumn::make('task_group_title'),
                Tables\Columns\TextColumn::make('task_item_group_title'),
                Tables\Columns\TextColumn::make('wtf_task_title'),
                Tables\Columns\TextColumn::make('expected_duration'),
                Tables\Columns\TextColumn::make('real_duration'),
                Tables\Columns\TextColumn::make('is_fulfilled'),
                // ->label(__('dispatch-report.table.columns.description.label'))
                //     ->formatStateUsing(fn($record) => Str::substr($record->description, 0, 30) . '...'),
                // Tables\Columns\TextColumn::make('state')
                //     ->label(__('dispatch-report.table.columns.state.label')),
                // Tables\Columns\TextColumn::make('author')
                //     ->label(__('dispatch-report.table.columns.author.label')),
            ])
            ->filters(WorkActivityReportTableFilter::make())
            ->headerActions([
                ExportAction::make()
                    ->exporter(WorkActivityReportExporter::class)
                    ->chunkSize(10000)
                    ->fileName(function ($livewire, DepartmentService $departmentSvc): string {
                        $filters = $livewire->getTableFiltersForm()->getState();

                        $from = data_get($filters, 'activity_date.activity_date_from');
                        $to   = data_get($filters, 'activity_date.activity_date_to');

                        $from = $from ? Carbon::parse($from)->format('ymd') : 'all';
                        $to   = $to ? Carbon::parse($to)->format('ymd') : 'all';
                        return "detail_praca_" . $departmentSvc->getActiveDepartment()?->code . "_{$from}_{$to}";
                    })
                    ->formats([
                        ExportFormat::Xlsx,
                    ])

            ])
            ->actions([])
            ->bulkActions([]);
    }
}
