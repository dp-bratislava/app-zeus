<?php

namespace App\Services\Activity\Activity;

use App\Models\ActivityAssignment;
use Dpb\Package\Tickets\Models\Ticket;
use Dpb\Package\Activities\Models\Activity;

class TicketService
{
    public function __construct(protected ActivityAssignment $activityAssignmentModel) {}

    public function getTicket(Activity $activity): Ticket|null
    {
        return $this->activityAssignmentModel
            ->with('subject')
            ->where('activity_id', '=', $activity->id)
            ->where('subject_type', '=', 'ticket')
            ->first()?->subject;
            // ->map(fn($assignment) => $assignment->subject);
    }

    // public function assignVehicle(Ticket $ticket, Vehicle $vehicle)
    // {
    //     $this->erService->createRelation($ticket, $vehicle, 'assigned');
    // }

    // // public function getVehicles(Ticket $ticket)
    // // {
    // //     return $this->erService->getTargetsOfType($ticket, Vehicle::class);
    // // }

    // public function getVehicle(Ticket $ticket)
    // {
    //     return $this->erService
    //         ->getTargetsOfType($ticket, Vehicle::class)
    //         // ->firstOrFail()
    //         ->first()
    //         ?->target;
    // }
}
