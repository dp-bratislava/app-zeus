<?php

namespace App\Filament\Resources\WorkActivityReportResource\Tables;

use App\Filament\Exports\Reports\CustomWorkActivityReportExporter;
use App\Filament\Exports\Reports\WorkActivityReportExporter;
use App\Jobs\Reports\ExportWorkActivityReportJob;
use App\Models\DispatchReport;
use App\Services\Fleet\VehicleService;
use App\Services\Inspection\CreateTicketService;
use App\Services\Inspection\AssignmentService as InspectionAssignmentService;
use App\Services\TS\TicketAssignmentService;
use App\States;
use Carbon\CarbonInterval;
use Dpb\Departments\Services\DepartmentService;
use Dpb\Package\Inspections\Models\Inspection;
use Dpb\PkgClosingSummary\Commands\GenerateExcelSpreadsheets;
use Filament\Actions\Exports\Enums\ExportFormat;
use Filament\Actions\Exports\Models\Export;
use Filament\Notifications\Notification;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\ExportAction;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class WorkActivityReportTabe
{
    public static function make(Table $table): Table
    {
        return $table
            ->heading(__('reports/work-activity-report.table.heading'))
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
                Tables\Columns\TextColumn::make('activity_date')
                    ->label(__('reports/work-activity-report.table.columns.activity_date'))
                    ->date('j.n.Y'),
                // ->label(__('dispatch-report.table.columns.date.label')),
                Tables\Columns\TextColumn::make('personal_id')
                    ->label(__('reports/work-activity-report.table.columns.personal_id')),
                Tables\Columns\TextColumn::make('last_name')
                    ->label(__('reports/work-activity-report.table.columns.full_name'))
                    ->formatStateUsing(fn($record): string => "{$record->last_name} {$record->first_name}"),
                // Tables\Columns\TextColumn::make('last_name')
                //     ->label(__('reports/work-activity-report.table.columns.last_name')),
                // Tables\Columns\TextColumn::make('first_name')
                //     ->label(__('reports/work-activity-report.table.columns.first_name')),
                Tables\Columns\TextColumn::make('department_code')
                    ->label(__('reports/work-activity-report.table.columns.department_code')),
                Tables\Columns\TextColumn::make('subject_code')
                    ->label(__('reports/work-activity-report.table.columns.subject_code.label'))
                    ->tooltip(__('reports/work-activity-report.table.columns.subject_code.tooltip')),
                Tables\Columns\TextColumn::make('task_group_title')
                    ->label(__('reports/work-activity-report.table.columns.task_group_title')),
                Tables\Columns\TextColumn::make('task_item_group_title')
                    ->label(__('reports/work-activity-report.table.columns.task_item_group_title')),
                Tables\Columns\TextColumn::make('wtf_task_title')
                    ->label(__('reports/work-activity-report.table.columns.wtf_task_title')),
                Tables\Columns\TextColumn::make('expected_duration')
                    ->label(__('reports/work-activity-report.table.columns.expected_duration'))
                    ->formatStateUsing(
                        fn($record): string => $record->expected_duration >= 0
                            ? CarbonInterval::seconds($record->expected_duration)->cascade()->format('%H:%I')
                            : CarbonInterval::seconds($record->real_duration)->cascade()->format('%H:%I')
                    ),
                Tables\Columns\TextColumn::make('real_duration')
                    ->label(__('reports/work-activity-report.table.columns.real_duration'))
                    ->formatStateUsing(fn($state): string => CarbonInterval::seconds($state)->cascade()->format('%H:%I')),
                Tables\Columns\TextColumn::make('is_fulfilled')
                    ->label(__('reports/work-activity-report.table.columns.is_fulfilled'))
                    ->formatStateUsing(
                        fn($state): string => match ($state) {
                            0 => 'Nie',
                            1 => 'Áno',
                            default => 'Nehodnotené',
                        }
                    ),
                // ->label(__('dispatch-report.table.columns.description.label'))
                //     ->formatStateUsing(fn($record) => Str::substr($record->description, 0, 30) . '...'),
                // Tables\Columns\TextColumn::make('state')
                //     ->label(__('dispatch-report.table.columns.state.label')),
                // Tables\Columns\TextColumn::make('author')
                //     ->label(__('dispatch-report.table.columns.author.label')),
            ])
            ->filters(WorkActivityReportTableFilter::make())
            ->headerActions([
                // ExportAction::make()
                //     ->exporter(WorkActivityReportExporter::class)
                //     ->chunkSize(10000)
                //     ->fileName(function ($livewire, DepartmentService $departmentSvc): string {
                //         $filters = $livewire->getTableFiltersForm()->getState();

                //         $from = data_get($filters, 'activity_date.activity_date_from');
                //         $to   = data_get($filters, 'activity_date.activity_date_to');

                //         $from = $from ? Carbon::parse($from)->format('ymd') : 'all';
                //         $to   = $to ? Carbon::parse($to)->format('ymd') : 'all';
                //         return "detail_praca_" . $departmentSvc->getActiveDepartment()?->code . "_{$from}_{$to}";
                //     })
                //     ->formats([
                //         ExportFormat::Xlsx,
                //     ])
                // Action::make('export')
                //     ->label('Export')
                //     ->icon('heroicon-o-arrow-down-tray')
                //     ->action(function ($livewire, DepartmentService $departmentSvc) {

                //         $filters = $livewire->getTableFiltersForm()->getState();

                //         $from = data_get($filters, 'activity_date.activity_date_from');
                //         $to   = data_get($filters, 'activity_date.activity_date_to');

                //         $fromFormatted = $from ? Carbon::parse($from)->format('ymd') : 'all';
                //         $toFormatted   = $to ? Carbon::parse($to)->format('ymd') : 'all';

                //         $fileName = "detail_praca_" .
                //             $departmentSvc->getActiveDepartment()?->code .
                //             "_{$fromFormatted}_{$toFormatted}.xlsx";

                //         // 👇 Your custom exporter logic
                //         $excel = app(CustomWorkActivityReportExporter::class)
                //             ->getExcelFile($filters);

                //         return response()->streamDownload(function () use ($excel) {
                //             $writer = new Xlsx($excel);
                //             $writer->save('php://output');
                //         }, $fileName);
                //     }),
                // Action::make('export')
                //     ->label('Export')
                //     ->icon('heroicon-o-arrow-down-tray')
                //     ->action(function ($livewire, CustomWorkActivityReportExporter $exporter) {

                //         $filters = $livewire->getTableFiltersForm()->getState();

                //         $fileName = 'work_activity_' . now()->format('Y_m_d_H_i_s') . '.xlsx';

                //         // return $exporter->stream($filters, $fileName);
                //         return $exporter->export($filters, $fileName);
                //     })
                Action::make('export')
                    ->label('Export')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->action(function ($livewire, CustomWorkActivityReportExporter $exporter) {

                        $filters = $livewire->getTableFiltersForm()->getState();

                        $fileName = 'work_activity_' . now()->format('Y_m_d_H_i_s') . '.xlsx';
                        // return response()->streamDownload(function () use ($filters, $exporter, $fileName) {
                        //     // return $exporter->stream($filters, $fileName);
                        //     $exporter->export($filters, $fileName);
                        // }, $fileName);
                        return $exporter->stream($filters, $fileName);
                        // ExportWorkActivityReportJob::dispatch($filters, $fileName, auth()->user()->id);

                        // Notification::make()
                        //     ->title('Export started')
                        //     ->body('You will be notified when it is ready.')
                        //     ->success()
                        //     ->send();
                    })
            ])
            ->actions([])
            ->bulkActions([]);
    }
}
