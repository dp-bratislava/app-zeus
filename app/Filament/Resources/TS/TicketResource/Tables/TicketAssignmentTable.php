<?php

namespace App\Filament\Resources\TS\TicketResource\Tables;

use App\Models\TicketAssignment;
use App\Services\Activity\Activity\WorkService;
use App\Services\TicketAssignmentRepository;
use App\Services\TS\ActivityService;
use App\States;
use App\StateTransitions\TS\CreatedToInProgress;
use App\StateTransitions\TS\InProgressToCancelled;
use Dpb\Package\Fleet\Models\Vehicle;
use Dpb\Package\Tickets\Models\Ticket;
use Filament\Forms\Components\DatePicker;
use Filament\Support\Colors\Color;
use Filament\Support\Enums\MaxWidth;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class TicketAssignmentTable
{
    public static function make(Table $table): Table
    {
        return $table
            ->heading(__('tickets/ticket.table.heading'))
            ->paginated([10, 25, 50, 100, 'all'])
            ->defaultPaginationPageOption(100)
            ->recordClasses(fn($record) => match ($record->ticket?->state?->getValue()) {
                States\TS\Ticket\Created::$name => 'bg-blue-200',
                States\TS\Ticket\Closed::$name => 'bg-green-200',
                States\TS\Ticket\Cancelled::$name => 'bg-gray-50',
                States\TS\Ticket\InProgress::$name => 'bg-yellow-200',
                default => null,
            })
            ->columns([
                Tables\Columns\TextColumn::make('ticket.id')
                    ->label(__('tickets/ticket.table.columns.id')),
                // date
                Tables\Columns\TextColumn::make('ticket.date')
                    ->date('j.n.Y')
                    ->label(__('tickets/ticket.table.columns.date')),
                // subject
                Tables\Columns\TextColumn::make('subject.code.code')
                    ->label(__('tickets/ticket.table.columns.subject')),
                // ->state(function ($record, TicketAssignment $svc) {
                //     return $svc->whereBelongsTo($record)->first()->subject?->code?->code;
                // }),
                // group type 
                Tables\Columns\TextColumn::make('ticket.group.title')
                    ->label(__('tickets/ticket.table.columns.group')),
                // title
                Tables\Columns\TextColumn::make('title')
                    ->label(__('tickets/ticket.table.columns.title')),
                // Tables\Columns\TextColumn::make('title')
                //     ->label(__('tickets/ticket.table.columns.title.label')),
                // deacription
                Tables\Columns\TextColumn::make('ticket.description')
                    ->label(__('tickets/ticket.table.columns.description'))
                    ->searchable()
                    ->grow(),
                // state
                Tables\Columns\SelectColumn::make('ticket.state')
                    ->label(__('tickets/ticket.table.columns.state'))
                    ->options([
                        States\TS\Ticket\Created::$name => __('tickets/ticket.states.created'),
                        States\TS\Ticket\Closed::$name => __('tickets/ticket.states.closed'),
                        States\TS\Ticket\Cancelled::$name => __('tickets/ticket.states.cancelled'),
                        States\TS\Ticket\InProgress::$name => __('tickets/ticket.states.in-progress'),
                    ]),
                // Tables\Columns\TextColumn::make('ticket.state')
                //     ->label(__('tickets/ticket.table.columns.state'))
                //     ->state(fn(TicketAssignment $record) => $record->ticket->state->label()),
                // ->state(fn($record) => dd($record)),
                // ->action(
                //     Action::make('select')
                //         ->requiresConfirmation()
                //         ->action(function (TicketAssignment $record): void {
                //             $ticket = $record->ticket;
                //             $ticket->state == 'created'
                //                 ? $ticket->state->transition(new CreatedToInProgress($ticket, auth()->guard()->user()))
                //                 : $ticket->state->transition(new InProgressToCancelled($ticket, auth()->guard()->user()));
                //         }),
                // ),
                // assigned to / maintenance group
                Tables\Columns\TextColumn::make('assignedTo.code')
                    ->badge()
                    ->label(__('tickets/ticket.table.columns.assigned_to.label'))
                    ->tooltip(__('tickets/ticket.table.columns.assigned_to.tooltip')),
                // place of occurance / ticket source
                Tables\Columns\TextColumn::make('ticket.source.title')
                    ->label(__('tickets/ticket.table.columns.source')),
                // Tables\Columns\TextColumn::make('department')
                //     ->label(__('tickets/ticket.table.columns.department.label'))
                //     ->state(function (HeaderService $svc, $record) {
                //         return $svc->getHeader($record)?->department?->code;
                //     }),
                Tables\Columns\TextColumn::make('activities')
                    ->label(__('tickets/ticket.table.columns.activities.label'))
                    ->tooltip(__('tickets/ticket.table.columns.activities.tooltip'))
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
                        $activities = $svc->getActivities($record->ticket);
                        $totalDuration = 0;
                        foreach ($activities as $activity) {
                            $totalDuration += $workService->getTotalDuration($activity);
                        }
                        $result = $svc->getTotalExpectedDuration($record->ticket) . ' min / ' . $totalDuration . ' min';
                        return $result;
                    }),
                // Tables\Columns\TextColumn::make('expenses')
                //     ->state(function ($record) {
                //         $result = $record->materials->sum(function ($material) {
                //             return $material->unit_price * $material->quantity;
                //         });
                //         return $result;
                //     }),

                Tables\Columns\TextColumn::make('expenses')
                    ->label(__('tickets/ticket.table.columns.total_expenses'))
                // ->state(function ($record) {
                // $total = 0;
                // if ($record->has('materials')) {
                //     $total += $record->materials->sum(function ($material) {
                //         return $material->price;
                //     });
                // }
                // if ($record->has('services')) {
                //     $services = $record->services->sum(function ($service) {
                //         return $service->price;
                //     });
                // }
                // return $total;
                // }),
                // Tables\Columns\TextColumn::make('man_minutes')
                //     ->state(function ($record) {
                //         $result = $record->activities->sum('duration');
                //         return $result;
                //     }),
            ])
            ->filters(TicketTableFilters::make())
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->modalWidth(MaxWidth::MaxContent) // options: sm, md, lg, xl, 2xl
                    // ->using(function (array $data, string $model, SubjectService $ticketSubjectSvc, HeaderService $ticketHeaderService): ?Model {
                    // ->using(function (array $data, string $model, CreateTicketService $ticketSvc): ?Model {
                    //     dd('hh');
                    //     return $ticketSvc->create($data);
                    // })

                    ->using(function (array $data, TicketAssignmentRepository $ticketAssignmentRepository): ?Model {
                        dd('hh');
                        return $ticketAssignmentRepository->create($data);
                    }),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                EditAction::make()
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    // Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
