<?php

namespace App\Filament\Resources\TS\TicketResource\Tables;

use App\Services\Activity\Activity\WorkService;
use App\Services\TS\ActivityService;
use App\Services\TS\CreateTicketService;
use App\Services\TS\HeaderService;
use App\Services\TS\SubjectService;
use App\States;
use App\StateTransitions\TS\CreatedToInProgress;
use App\StateTransitions\TS\InProgressToCancelled;
use Dpb\Package\Tickets\Models\Ticket;
use Filament\Support\Colors\Color;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class TicketTable
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
