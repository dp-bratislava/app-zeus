<?php

namespace App\Filament\Resources\TS\TicketItemResource\Tables;

use App\Services\Activity\Activity\WorkService;
use App\Services\TS\ActivityService;
use App\Services\TS\CreateTicketService;
use App\Services\TS\HeaderService;
use App\Services\TS\SubjectService;
use App\Services\TS\TicketAssignmentService;
use App\States;
use App\StateTransitions\TS\TicketItem\CreatedToInProgress;
use App\StateTransitions\TS\TicketItem\InProgressToCancelled;
use Dpb\Package\Tickets\Models\Ticket;
use Dpb\Package\Tickets\Models\TicketItem;
use Filament\Support\Colors\Color;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class TicketItemTable
{
    public static function make(Table $table): Table
    {
        return $table
            ->paginated([10, 25, 50, 100, 'all'])
            ->defaultPaginationPageOption(100)
            ->recordClasses(fn($record) => match ($record->state?->getValue()) {
                States\TS\Ticket\Created::$name => 'bg-blue-200',
                States\TS\Ticket\Closed::$name => 'bg-green-200',
                States\TS\Ticket\Cancelled::$name => 'bg-gray-50',
                States\TS\Ticket\InProgress::$name => 'bg-yellow-200',
                default => null,
            })
            // ->groups([
            //     Tables\Grouping\Group::make('author.name')
            //         ->collapsible(),
            // ])
            ->defaultGroup('ticket_id')
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label(__('tickets/ticket-item.table.columns.id.label')),
                Tables\Columns\TextColumn::make('date')->date()
                    ->label(__('tickets/ticket-item.table.columns.date.label')),
                // Tables\Columns\TextColumn::make('parent.id')
                //     ->label(__('tickets/ticket-item.table.columns.parent.label')),
                Tables\Columns\TextColumn::make('title')
                    ->label(__('tickets/ticket-item.table.columns.title.label')),
                Tables\Columns\TextColumn::make('description')
                    ->label(__('tickets/ticket-item.table.columns.description.label')),
                Tables\Columns\TextColumn::make('state')
                    ->label(__('tickets/ticket-item.table.columns.state.label'))
                    ->state(fn(TicketItem $record) => $record->state->label())
                    // ->state(fn($record) => dd($record)),
                    ->action(
                        Action::make('select')
                            ->requiresConfirmation()
                            ->action(function (TicketItem $record): void {
                                $record->state == 'created'
                                    ? $record->state->transition(new CreatedToInProgress($record, auth()->guard()->user()))
                                    : $record->state->transition(new InProgressToCancelled($record, auth()->guard()->user()));
                            }),
                    ),
                // TextColumn::make('department.code'),
                Tables\Columns\TextColumn::make('subject')
                    ->label(__('tickets/ticket-item.table.columns.subject.label'))
                    ->state(function (TicketItem $record, TicketAssignmentService $svc) {
                        if ($record->ticket !== null) {
                            return $svc->getSubject($record->ticket)?->code?->code;
                        }
                    }),
                Tables\Columns\TextColumn::make('source')
                    ->label(__('tickets/ticket-item.table.columns.source.label'))
                    ->state(function (TicketItem $record, TicketAssignmentService $svc) {
                        if ($record->ticket !== null) {
                            return $svc->getSourceLabel($record->ticket);
                        }
                    })
                    ->badge(),
                Tables\Columns\TextColumn::make('department')
                    ->label(__('tickets/ticket-item.table.columns.department.label')),
                // ->state(function (HeaderService $svc, $record) {
                //     return $svc->getHeader($record->ticket)?->department?->code;
                // }),
                // Tables\Columns\TextColumn::make('activities')
                //     ->label(__('tickets/ticket-item.table.columns.activities.label'))
                //     ->tooltip(__('tickets/ticket-item.table.columns.activities.tooltip'))
                //     ->state(function ($record, ActivityService $svc, WorkService $workService) {
                //         // $result = $svc->getActivities($record)?->map(function ($activity) use ($workService) {
                //         //     // dd($workService->getWorkIntervals($activity));
                //         //     return $activity->template->title
                //         //         . ' #' . $activity->template->duration
                //         //         . '/' . $workService->getWorkIntervals($activity)?->sum(function($work) {
                //         //             // return $work;
                //         //             return $work?->duration;
                //         //             // return print_r($work?->duration);
                //         //         });
                //         // });
                //         $activities = $svc->getActivities($record->ticket);
                //         $totalDuration = 0;
                //         foreach ($activities as $activity) {
                //             $totalDuration += $workService->getTotalDuration($activity);
                //         }
                //         $result = $svc->getTotalExpectedDuration($record->ticket) . ' min / ' . $totalDuration . ' min';
                //         return $result;
                //     }),
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
                Tables\Columns\TextColumn::make('ticket.id')
                    ->label(__('tickets/ticket-item.table.columns.ticket.label'))
                    ->tooltip(fn(TicketItem $record) => $record?->ticket?->title)
                    ->badge(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make()
                    ->mutateRecordDataUsing(function (
                        $record,
                        array $data,
                        SubjectService $subjectSvc,
                        HeaderService $headerSvc,
                        ActivityService $activitySvc
                    ): array {
                        $data['subject_id'] = $subjectSvc->getSubject($record)?->id;
                        $data['department_id'] = $headerSvc->getHeader($record)?->department?->id;
                        $data['source_id'] = $record?->source?->id;
                        // $activities = $activitySvc->getActivities($record);
                        // foreach ($activities as $activity) {
                        //     $data['activities'][] = [
                        //             'id' => $activity->id,
                        //             'date' => $activity->date,
                        //             // 'activity_template_id' => $activity->template_id
                        //     ];
                        // }
                        // $data['activities'] = $activitySvc->getActivities($record)
                        //     ->map(function($activity) {
                        //         return [
                        //             'date' => $activity->date,
                        //             'activity_template_id' => $activity->template_id
                        //         ];
                        //     });
                        $data['activities'] = $activitySvc->getActivities($record);
                        return $data;
                    })
                    ->using(function (Model $record, array $data, CreateTicketService $ticketSvc): ?Model {
                        return $ticketSvc->update($record, $data);
                    })
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
