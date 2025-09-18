<?php

namespace App\Services\Ticket;

use App\Models\ActivityAssignment;
use Dpb\Package\Tickets\Models\Ticket;

class ActivityService
{
    public function __construct(protected ActivityAssignment $activityAssignmentModel) {}

    // public function assignUnitRate(ActivityTemplate $tempalte, UnitRate $unitRate)
    // {
    //     $this->erService->createRelation($ticket, $vehicle, 'assigned');
    // }

    public function getActivities(Ticket $ticket)
    {        
        return $this->activityAssignmentModel
            ->with('activity')
            ->where('subject_id', '=', $ticket->id)
            ->where('subject_type', '=', 'ticket')
            ->get()
            ->map(fn($assignment) => $assignment->activity);
    }
}
