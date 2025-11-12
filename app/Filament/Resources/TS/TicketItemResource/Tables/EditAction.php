<?php

namespace App\Filament\Resources\TS\TicketItemResource\Tables;

use App\Models\ActivityAssignment;
use App\Models\TicketAssignment;
use App\Models\TicketItemAssignment;
use App\Models\WorkAssignment;
use App\Services\TicketItemRepository;
use Filament\Support\Enums\MaxWidth;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\EditAction as ActionsEditAction;
use Illuminate\Database\Eloquent\Model;

class EditAction
{
    public static function make(): ActionsEditAction
    {
        return ActionsEditAction::make()
            ->modalWidth(MaxWidth::class)
            // ->modalHeading(fn($record) => dd($record->ticke))
            ->mutateRecordDataUsing(function (
                $record,
                array $data,
                TicketAssignment $ticketAssignment,
                TicketItemAssignment $ticketItemAssignment,
                ActivityAssignment $activityAssignment,
                WorkAssignment $workAssignment
            ): array {
                // subject
                $subjectId = $ticketAssignment->whereBelongsTo($record->ticket)->first()?->subject?->id;
                $data['subject_id'] = $subjectId;

                // activities
                $activities = $activityAssignment->whereMorphedTo('subject', $record)
                    ->with(['activity', 'activity.template'])
                    ->get()
                    ->map(fn($assignment) => $assignment->activity);
                $data['activities'] = $activities;
                // dd($activities);

                // work 
                foreach ($data['activities'] as $key => $activity) {
                    $workAssignments = $workAssignment->whereMorphedTo('subject', $activity)
                        ->with(['workInterval', 'employeeContract'])
                        ->get()
                        ->toArray();
                    // ->map(fn($assignment) => $assignment->workInterval);                            
                    $data['activities'][$key]['workAssignments'] = $workAssignments;
                    // dd($workAssignments);
                }

                // assigned to
                $assignedToId = $ticketItemAssignment->whereBelongsTo($record, 'ticketItem')->first()?->assignedTo?->id;
                $data['assigned_to'] = $assignedToId;
                // dd($data);
                return $data;
            })
            ->using(function (Model $record, array $data, TicketItemRepository $ticketItemRepo): ?Model {
                // dd($data);
                return $ticketItemRepo->update($record, $data);
            });
    }
}
