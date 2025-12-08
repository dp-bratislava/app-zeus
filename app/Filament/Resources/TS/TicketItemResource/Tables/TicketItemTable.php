<?php

namespace App\Filament\Resources\TS\TicketItemResource\Tables;

use App\Filament\Resources\TS\TicketResource\RelationManagers\TicketItemRelationManager;
use App\Models\ActivityAssignment;
use App\Models\TicketAssignment;
use App\Models\TicketItemAssignment;
use App\Models\WorkAssignment;
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
use Filament\Support\Enums\MaxWidth;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class TicketItemTable
{
    public static function make(Table $table): Table
    {
        return $table
            ->heading(__('tickets/ticket.relation_manager.ticket_items.table.heading'))
            ->emptyStateHeading(__('tickets/ticket.relation_manager.ticket_items.table.empty_state_heading'))
            ->emptyStateDescription(__('tickets/ticket.relation_manager.ticket_items.table.empty_state_description'))
            ->paginated([10, 25, 50, 100, 'all'])
            ->defaultPaginationPageOption(100)
            ->recordClasses(fn($record) => match ($record->state?->getValue()) {
                States\TS\Ticket\Created::$name => 'bg-blue-200',
                States\TS\Ticket\Closed::$name => 'bg-green-200',
                States\TS\Ticket\Cancelled::$name => 'bg-gray-50',
                States\TS\Ticket\InProgress::$name => 'bg-yellow-200',
                States\TS\TicketItem\AwaitingParts::$name => 'bg-red-200',
                default => null,
            })
            // ->groups([
            //     Tables\Grouping\Group::make('author.name')
            //         ->collapsible(),
            // ])
            // ->defaultGroup('ticket_id')
            ->defaultGroup(TicketItemRelationManager::class ? null : 'ticket_id')
            ->columns([
                // ticket id
                // Tables\Columns\TextColumn::make('ticket.id')
                //     ->label(__('tickets/ticket-item.table.columns.ticket.label'))
                //     ->tooltip(fn(TicketItem $record) => $record?->ticket?->title)
                //     ->badge(),
                // ticket item code id
                Tables\Columns\TextColumn::make('code')
                    ->label(__('tickets/ticket-item.table.columns.code.label'))
                    ->grow(false),
                Tables\Columns\TextColumn::make('date')->date()
                    ->label(__('tickets/ticket-item.table.columns.date.label'))
                    ->grow(false),
                // Tables\Columns\TextColumn::make('parent.id')
                //     ->label(__('tickets/ticket-item.table.columns.parent.label')),
                // title 
                Tables\Columns\TextColumn::make('group.title')
                    ->label(__('tickets/ticket-item.table.columns.group.label')),
                // Tables\Columns\TextColumn::make('title')
                //     ->label(__('tickets/ticket-item.table.columns.title.label')),
                Tables\Columns\TextColumn::make('description')
                    ->label(__('tickets/ticket-item.table.columns.description.label'))
                    ->grow(),
                Tables\Columns\TextColumn::make('state')
                    ->label(__('tickets/ticket-item.table.columns.state.label'))
                    ->state(fn(TicketItem $record) => $record?->state?->label()),
                // ->state(fn($record) => dd($record)),
                // ->action(
                //     Action::make('select')
                //         ->requiresConfirmation()
                //         ->action(function (TicketItem $record): void {
                //             $record->state == 'created'
                //                 ? $record->state->transition(new CreatedToInProgress($record, auth()->guard()->user()))
                //                 : $record->state->transition(new InProgressToCancelled($record, auth()->guard()->user()));
                //         }),
                // ),
                // TextColumn::make('department.code'),
                Tables\Columns\TextColumn::make('subject')
                    ->label(__('tickets/ticket-item.table.columns.subject.label'))
                    ->state(function (TicketItem $record, TicketAssignmentService $svc) {
                        if ($record->ticket !== null) {
                            return $svc->getSubject($record->ticket)?->code?->code;
                        }
                    })
                    ->hiddenOn(TicketItemRelationManager::class),
                Tables\Columns\TextColumn::make('source')
                    ->label(__('tickets/ticket-item.table.columns.source.label'))
                    ->state(function (TicketItem $record, TicketAssignmentService $svc) {
                        if ($record->ticket !== null) {
                            return $svc->getSourceLabel($record->ticket);
                        }
                    })
                    ->hiddenOn(TicketItemRelationManager::class)
                    ->badge(),
                Tables\Columns\TextColumn::make('assigned_to')
                    ->label(__('tickets/ticket-item.table.columns.assigned_to.label'))
                    ->state(function (TicketItem $record, TicketItemAssignment $ticketItemAssignment) {
                        return $ticketItemAssignment->whereBelongsTo($record, 'ticketItem')->first()?->assignedTo?->code;
                    })
                    ->badge()
                    ->color(fn(string $state) => match ($state) {
                        '1TPA' => '#888',
                        default => '#333'
                    }),
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

            ])
            ->filters([
                //
            ])
            ->actions([
                ViewAction::make(),
                EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    // Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
