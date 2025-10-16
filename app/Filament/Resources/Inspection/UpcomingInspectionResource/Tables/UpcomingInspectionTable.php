<?php

namespace App\Filament\Resources\Inspection\UpcomingInspectionResource\Tables;

use App\Services\Fleet\VehicleService;
use App\Services\Inspection\CreateTicketService;
use App\Services\Inspection\SubjectService;
use App\States;
use Dpb\Package\Inspections\Models\Inspection;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Collection;

class UpcomingInspectionTable
{
    public static function make(Table $table): Table
    {
        return $table
            ->paginated([10, 25, 50, 100, 'all'])
            ->defaultPaginationPageOption(100)
            ->recordClasses(fn($record) => match ($record->state?->getValue()) {
                States\Inspection\Upcoming::$name => 'bg-blue-200',
                States\Inspection\InProgress::$name => 'bg-yellow-200',
                default => null,
            })
            ->columns([
                Tables\Columns\TextColumn::make('date')->date('j.n.Y')
                    ->label(__('inspections/upcoming-inspection.table.columns.date.label')),
                Tables\Columns\TextColumn::make('subject')
                    ->label(__('tickets/ticket.table.columns.subject.label'))
                    ->state(function ($record, SubjectService $svc) {
                        return $svc->getSubject($record)?->code?->code;
                    }),
                Tables\Columns\TextColumn::make('template.title')
                    ->label(__('inspections/upcoming-inspection.table.columns.template.label')),
                Tables\Columns\TextColumn::make('state')
                    ->label(__('inspections/upcoming-inspection.table.columns.state.label'))
                    ->state(fn(Inspection $record) => $record->state->label()),
                Tables\Columns\TextColumn::make('subject.maintenanceGroup.title')
                    ->label(__('inspections/upcoming-inspection.table.columns.maintenance_group.label')),
                Tables\Columns\TextColumn::make('note')
                    ->label(__('inspections/upcoming-inspection.table.columns.note.label')),
                Tables\Columns\TextColumn::make('distance_traveled')
                    ->label(__('inspections/upcoming-inspection.table.columns.distance_traveled.label'))
                    ->state(function ($record, VehicleService $vehicleService, SubjectService $subjectService) {
                        $vehicle = $subjectService->getSubject($record);
                        return round($vehicleService->getInspectionDistanceTraveled($vehicle), 2);
                    }),
                Tables\Columns\TextColumn::make('due_distance')
                    ->label(__('inspections/upcoming-inspection.table.columns.due_distance.label')),
                Tables\Columns\TextColumn::make('km_to_due_distance')
                    ->label(__('inspections/upcoming-inspection.table.columns.km_to_due_distance.label')),
                Tables\Columns\TextColumn::make('due_date')
                    ->label(__('inspections/upcoming-inspection.table.columns.due_date.label')),
                Tables\Columns\TextColumn::make('days_to_due_date')
                    ->label(__('inspections/upcoming-inspection.table.columns.days_to_due_date.label')),
            ])
            ->filters([
                //
            ])
            ->actions([
                // Tables\Actions\ViewAction::make(),
                // Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('create_ticket')
                    ->label(__('inspections/upcoming-inspection.table.actions.create_ticket'))
                    ->action(function ($record, CreateTicketService $createTicketService) {
                        $createTicketService->createTicket($record);
                    })
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
                    Tables\Actions\Action::make('bulk_create_tickets')
                        ->label(__('inspections/upcoming-inspection.table.actions.bulk_create_tickets'))
                        ->action(function (Collection $records, CreateTicketService $createTicketService) {
                            foreach ($records as $record) {
                                $createTicketService->createTicket($record);
                            }
                        })
                ]),
            ]);
    }
}
