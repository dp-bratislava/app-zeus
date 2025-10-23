<?php

namespace App\Services\TS;

use App\Models\ActivityAssignment;
use Dpb\Package\Tickets\Models\Ticket;
use Illuminate\Support\Collection;

class ActivityService
{
    public function __construct(protected ActivityAssignment $activityAssignmentModel) {}

    public function addActivities(Ticket $ticket, Collection $activities)
    {       
        foreach ($activities as $key => $activity) {
            $this->activityAssignmentModel->create([
                'activity_id' => $activity->id,
                'subject_id' => $ticket->id,
                'subject_type' => $ticket->getMorphClass()
            ]);
        } 
    }

    public function syncActivities(Ticket $ticket, Collection $activities): Collection
    {        
        return $this->activityAssignmentModel
            ->where('subject_id', '=', $ticket->id)
            ->where('subject_type', '=', 'ticket')
            ->sync($activities);
    }

    public function getActivities(Ticket $ticket): Collection
    {        
        return $this->activityAssignmentModel
            ->with(['activity', 'activity.template'])
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
