<?php

namespace App\Services\Ticket;

use App\Models\ActivityAssignment;
use Dpb\Package\Tickets\Models\Ticket;
use Illuminate\Support\Collection;

class ActivityService
{
    public function __construct(protected ActivityAssignment $activityAssignmentModel) {}

    // public function assignUnitRate(ActivityTemplate $tempalte, UnitRate $unitRate)
    // {
    //     $this->erService->createRelation($ticket, $vehicle, 'assigned');
    // }

    public function getActivities(Ticket $ticket): Collection
    {        
        return $this->activityAssignmentModel
            ->with('activity')
            ->where('subject_id', '=', $ticket->id)
            ->where('subject_type', '=', 'ticket')
            ->get()
            ->map(fn($assignment) => $assignment->activity);
    }

    public function getTotalExpectedDuration(Ticket $ticket): int
    {        
        return $this->activityAssignmentModel
            ->with('activity.template')
            ->where('subject_id', '=', $ticket->id)
            ->where('subject_type', '=', 'ticket')
            ->get()
            ->sum('activity.template.duration');
    }    
}
