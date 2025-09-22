<?php

namespace App\Services\Activity\Activity;

use App\Models\WorkAssignment;
use Dpb\Package\Activities\Models\Activity;

class WorkService
{
    public function __construct(protected WorkAssignment $workAssignment) {}

    public function getWorkIntervals(Activity $activity)
    {
        return $this->workAssignment
            ->with(['workInterval', 'employeeContract'])
            ->where('subject_id', '=', $activity->id)
            ->where('subject_type', '=', 'activity')
            ->get()
            ->map(fn($assignment) => $assignment->workInterval);
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
