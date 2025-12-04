<?php

namespace App\Filament\Resources\Inspection\InspectionResource\Tables;

use App\Models\InspectionAssignment;
use App\Services\Inspection\AssignmentService as InspectionAssignmentService;
use App\States;
use Dpb\Package\Inspections\Models\Inspection;
use Filament\Tables;
use Filament\Tables\Table;

class InspectionAssignmentTable
{
    public static function make(Table $table): Table
    {
        return $table
            ->heading(__('inspections/inspection.table.heading'))
            ->paginated([10, 25, 50, 100, 'all'])
            ->defaultPaginationPageOption(100)
            ->recordClasses(fn($record) => match ($record->inspection->state?->getValue()) {
                States\Inspection\Upcoming::$name => 'bg-blue-200',
                States\Inspection\InProgress::$name => 'bg-yellow-200',
                States\Inspection\Overdue::$name => 'bg-red-200',
                default => null,
            })
            ->columns([
                // date
                Tables\Columns\TextColumn::make('inspection.date')
                    ->label(__('inspections/inspection.table.columns.date'))
                    ->date('j.n.Y'),
                // subject
                Tables\Columns\TextColumn::make('subject.code.code')
                    ->label(__('inspections/inspection.table.columns.subject')),
                // template
                Tables\Columns\TextColumn::make('inspection.template.title')
                    ->label(__('inspections/inspection.table.columns.template')),
                // state
                Tables\Columns\TextColumn::make('inspection.state')
                    ->label(__('inspections/inspection.table.columns.state'))
                    ->state(fn(InspectionAssignment $record) => $record->inspection->state->label()),
                // finished at
                Tables\Columns\TextColumn::make('finished_at')
                    ->label(__('inspections/inspection.table.columns.finished_at')),
                // maintenance group
                Tables\Columns\TextColumn::make('subject.maintenanceGroup.code')
                    ->label(__('inspections/inspection.table.columns.maintenance_group')),
                // ->state(function ($record, InspectionAssignmentService $svc) {
                //     return $svc->getSubject($record)?->maintenanceGroup?->code;
                // }),
                // note
                Tables\Columns\TextColumn::make('note')
                    ->label(__('inspections/inspection.table.columns.note')),
                // distance traveled
                Tables\Columns\TextColumn::make('distance_traveled')
                    ->label(__('inspections/inspection.table.columns.distance_traveled')),
            ])
            ->filters(InspectionTableFilters::make())
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make()
                // ->mutateFormDataUsing(function (ActivityService $svc, array $data, Ticket $record) {
                //         ->(function (ActivityService $svc, array $data, Ticket $record) {
                //                     $activities = app(ActivityService::class)->getActivities($record)->toArray();

                // $data['activities'] = $activities;
                // dd($activities);
                // })
                // ->after(function (TicketService $ticketService, Department $departmentHdl, array $data, Ticket $record) {
                // ->after(function ($action, $record) {
                //     // $department = $departmentHdl->findOrFail($data['department_id']);
                //     dd($action);
                //     $ticketService->assignDepartment($record, $department);
                // }),
                // Tables\Actions\ReplicateAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    // Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
