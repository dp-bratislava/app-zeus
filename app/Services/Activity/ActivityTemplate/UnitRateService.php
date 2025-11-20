<?php

namespace App\Services\Activity\ActivityTemplate;

use App\Models\UnitRate;
use Dpb\DatahubSync\Models\Department;
use Dpb\Package\Activities\Models\ActivityTemplate;
use Dpb\PkgTickets\Models\Ticket;
use Dpb\Packages\Vehicles\Models\Vehicle;

// use Illuminate\Database\Eloquent\Collection;

class UnitRateService
{
    // public function assignUnitRate(ActivityTemplate $tempalte, UnitRate $unitRate)
    // {
    //     $this->erService->createRelation($ticket, $vehicle, 'assigned');
    // }

    public function getUnitRate(ActivityTemplate $template): UnitRate|null
    {
        return UnitRate::whereLike('rateable_type', 'activity-template')
            ->where('rateable_id', '=', $template->id)
            ->first();
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
