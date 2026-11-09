<?php

namespace Dpb\Modules\Inspections\Filament\Resources\InspectionAssignments\Tables;

use Dpb\Modules\Inspections\Filament\Resources\InspectionAssignments\Actions\CreateTaskAction;
use Dpb\Modules\Inspections\Filament\Resources\InspectionAssignments\Actions\EditTaskAction;
use Dpb\Package\TaskMS\Models\InspectionAssignment;
use Dpb\Package\TaskMS\States;
use Dpb\Package\TaskMS\Repositories\DepartmentAssignmentRepository;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class InspectionAssignmentTable
{
    public static function make(Table $table): Table
    {
        return $table
            ->lazyLoaded()
            ->modifyQueryUsing(function (
                Builder $query,
                DepartmentAssignmentRepository $daRepo
            ) {
                $query
                    ->byVehicleTypeIds($daRepo->getVehicleTypeIdsByActiveDepartment())
                    ->with([
                        'inspection',
                        'inspection.template',
                        'subject',
                        'subject.codes' => fn($q) => $q->wherePivot('date_to', null),
                        'subject.maintenanceGroup',
                    ]);

                return $query;
            })
            ->heading(__('dpb-mod-inspections::inspection-assignment.table.heading'))
            ->emptyStateHeading(__('dpb-mod-inspections::inspection-assignment.table.empty_state_heading'))
            ->paginated([10, 25, 50, 100, 'all'])
            ->defaultPaginationPageOption(50)
            ->defaultSort('created_at', 'desc')
            ->recordClasses(fn($record) => match ($record->inspection->state) {
                States\Inspection\Upcoming::$name => 'bg-blue-200',
                States\Inspection\InProgress::$name => 'bg-yellow-200',
                States\Inspection\Overdue::$name => 'bg-red-200',
                default => null,
            })
            ->columns([
                TextColumn::make('inspection.date')
                    ->label(__('dpb-mod-inspections::inspection-assignment.table.columns.date'))
                    ->date('d.m.Y'),
                TextColumn::make('subject.code.code')
                    ->label(__('dpb-mod-inspections::inspection-assignment.table.columns.subject')),
                TextColumn::make('inspection.template.title')
                    ->label(__('dpb-mod-inspections::inspection-assignment.table.columns.template')),
                TextColumn::make('inspection.state')
                    ->label(__('dpb-mod-inspections::inspection-assignment.table.columns.state'))
                    ->state(fn(InspectionAssignment $record) => $record->inspection->state->label()),
                TextColumn::make('finished_at')
                    ->label(__('dpb-mod-inspections::inspection-assignment.table.columns.finished_at')),
                TextColumn::make('subject.maintenanceGroup.code')
                    ->label(__('dpb-mod-inspections::inspection-assignment.table.columns.maintenance_group')),
                TextColumn::make('note')
                    ->label(__('dpb-mod-inspections::inspection-assignment.table.columns.note')),
                TextColumn::make('distance_traveled')
                    ->label(__('dpb-mod-inspections::inspection-assignment.table.columns.distance_traveled')),
            ])
            // ->filters(InspectionAssignmentTableFilters::make())
            ->headerActions([
                \Filament\Actions\CreateAction::make(),
            ])
            ->recordActions([
                \Filament\Actions\EditAction::make(),
                CreateTaskAction::make('create_task'),
                EditTaskAction::make('edit_task'),
            ]);
    }
}
