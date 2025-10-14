<?php

namespace App\Filament\Resources\Inspection\InspectionResource\Tables;

use App\Services\Inspection\SubjectService;
use App\States;
use Dpb\Package\Inspections\Models\Inspection;
use Filament\Tables;
use Filament\Tables\Table;

class InspectionTable
{
    public static function make(Table $table): Table
    {
        return $table
            ->paginated([10, 25, 50, 100, 'all'])
            ->defaultPaginationPageOption(100)
            ->recordClasses(fn($record) => match ($record->state?->getValue()) {
                States\Inspection\Upcoming::$name => 'bg-blue-200',
                States\Inspection\InProgress::$name => 'bg-yellow-200',
                States\Inspection\Overdue::$name => 'bg-red-200',
                default => null,
            })
            ->columns([
                Tables\Columns\TextColumn::make('date')->date()
                    ->label(__('inspections/inspection.table.columns.date.label')),
                Tables\Columns\TextColumn::make('subject')
                    ->label(__('tickets/ticket.table.columns.subject.label'))
                    ->state(function ($record, SubjectService $svc) {
                        return $svc->getSubject($record)?->code?->code;
                    }),
                Tables\Columns\TextColumn::make('template.title')
                    ->label(__('inspections/inspection.table.columns.template.label')),
                Tables\Columns\TextColumn::make('state')
                    ->label(__('inspections/inspection.table.columns.state.label'))
                    ->state(fn(Inspection $record) => $record->state->label()),
                Tables\Columns\TextColumn::make('finished_at')
                    ->label(__('inspections/inspection.table.columns.finished_at.label')),
                Tables\Columns\TextColumn::make('subject.maintenanceGroup.title')
                    ->label(__('inspections/inspection.table.columns.maintenance_group.label')),
                Tables\Columns\TextColumn::make('note')
                    ->label(__('inspections/inspection.table.columns.note.label')),
                Tables\Columns\TextColumn::make('distance_traveled')
                    ->label(__('inspections/inspection.table.columns.distance_traveled.label')),
            ])
            ->filters([
                //
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
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
