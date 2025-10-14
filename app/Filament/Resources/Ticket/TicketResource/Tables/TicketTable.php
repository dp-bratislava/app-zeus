<?php

namespace App\Filament\Resources\Ticket\TicketResource\Tables;

use App\Services\Activity\Activity\WorkService;
use App\Services\Ticket\ActivityService;
use App\Services\Ticket\HeaderService;
use App\Services\Ticket\SubjectService;
use App\States;
use App\StateTransitions\Ticket\CreatedToInProgress;
use App\StateTransitions\Ticket\InProgressToCancelled;
use Dpb\Package\Tickets\Models\Ticket;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Table;

class TicketTable
{
    public static function make(Table $table): Table
    {
        return $table
            ->paginated([10, 25, 50, 100, 'all'])
            ->defaultPaginationPageOption(100)
            ->recordClasses(fn($record) => match ($record->state?->getValue()) {
                States\Ticket\Created::$name => 'bg-blue-200',
                States\Ticket\Closed::$name => 'bg-green-200',
                States\Ticket\Cancelled::$name => 'bg-gray-50',
                States\Ticket\InProgress::$name => 'bg-yellow-200',
                default => null,
            })
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label(__('tickets/ticket.table.columns.id.label')),
                Tables\Columns\TextColumn::make('date')->date()
                    ->label(__('tickets/ticket.table.columns.date.label')),
                Tables\Columns\TextColumn::make('parent.id')
                    ->label(__('tickets/ticket.table.columns.parent.label')),
                Tables\Columns\TextColumn::make('title')
                    ->label(__('tickets/ticket.table.columns.title.label')),
                Tables\Columns\TextColumn::make('description')
                    ->label(__('tickets/ticket.table.columns.description.label')),
                Tables\Columns\TextColumn::make('state')
                    ->label(__('tickets/ticket.table.columns.state.label'))
                    ->state(fn(Ticket $record) => $record->state->label())
                    // ->state(fn($record) => dd($record)),
                    ->action(
                        Action::make('select')
                            ->requiresConfirmation()
                            ->action(function (Ticket $record): void {
                                $record->state == 'created'
                                    ? $record->state->transition(new CreatedToInProgress($record, auth()->guard()->user()))
                                    : $record->state->transition(new InProgressToCancelled($record, auth()->guard()->user()));
                            }),
                    ),
                // TextColumn::make('department.code'),
                Tables\Columns\TextColumn::make('subject')
                    ->label(__('tickets/ticket.table.columns.subject.label'))
                    ->state(function ($record, SubjectService $svc) {
                        return $svc->getSubject($record)?->code?->code;
                    }),
                Tables\Columns\TextColumn::make('source.title')
                    ->label(__('tickets/ticket.table.columns.source.label')),
                Tables\Columns\TextColumn::make('department')
                    ->label(__('tickets/ticket.table.columns.department.label'))
                    ->state(function (HeaderService $svc, $record) {
                        return $svc->getHeader($record)?->department?->code;
                    }),
                Tables\Columns\TextColumn::make('activities')
                    ->label(__('tickets/ticket.table.columns.activities.label'))
                    ->state(function ($record, ActivityService $svc, WorkService $workService) {
                        // $result = $svc->getActivities($record)?->map(function ($activity) use ($workService) {
                        //     // dd($workService->getWorkIntervals($activity));
                        //     return $activity->template->title
                        //         . ' #' . $activity->template->duration
                        //         . '/' . $workService->getWorkIntervals($activity)?->sum(function($work) {
                        //             // return $work;
                        //             return $work?->duration;
                        //             // return print_r($work?->duration);
                        //         });
                        // });
                        $activities = $svc->getActivities($record);
                        $totalDuration = 0;
                        foreach ($activities as $activity) {
                            $totalDuration += $workService->getTotalDuration($activity);
                        }
                        $result = $svc->getTotalExpectedDuration($record) . ' min / ' . $totalDuration . ' min';
                        return $result;
                    }),
                // Tables\Columns\TextColumn::make('expenses')
                //     ->state(function ($record) {
                //         $result = $record->materials->sum(function ($material) {
                //             return $material->unit_price * $material->quantity;
                //         });
                //         return $result;
                //     }),

                // Tables\Columns\TextColumn::make('expenses')
                //     ->state(function ($record) {
                //         $materials = $record->materials->sum(function ($material) {
                //             return $material->price;
                //         });
                //         $services = $record->services->sum(function ($service) {
                //             return $service->price;
                //         });
                //         return $materials + $services;
                //     }),
                // Tables\Columns\TextColumn::make('man_minutes')
                //     ->state(function ($record) {
                //         $result = $record->activities->sum('duration');
                //         return $result;
                //     }),
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
