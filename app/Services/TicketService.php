<?php

namespace App\Services;

use Dpb\DatahubSync\Models\Department;
use Dpb\PkgEntityManager\Models\EntityRelation;
use Dpb\PkgEntityManager\Services\EntityRelationService;
use Dpb\PkgTickets\Models\Ticket;
use Dpb\Packages\Vehicles\Models\Vehicle;
use Dpb\Packages\Vehicles\Models\VehicleType;
use Illuminate\Support\Collection;

// use Illuminate\Database\Eloquent\Collection;

class TicketService
{
    public function __construct(protected EntityRelationService $erService) {}

    public function assignVehicle(Ticket $ticket, Vehicle $vehicle)
    {
        $this->erService->createRelation($ticket, $vehicle, 'assigned');
    }

    // public function getVehicles(Ticket $ticket)
    // {
    //     return $this->erService->getTargetsOfType($ticket, Vehicle::class);
    // }

    public function getVehicle(Ticket $ticket)
    {
        return $this->erService
            ->getTargetsOfType($ticket, Vehicle::class)
            // ->firstOrFail()
            ->first()
            ?->target;
    }

    public function assignDepartment(Ticket $ticket, Department $department)
    {
        $this->erService->createRelation($ticket, $department, 'assigned');
    }    

    public function getDepartment(Ticket $ticket): Department
    {
        return $this->erService
            ->getTargetsOfType($ticket, Department::class)
            ->firstOrFail()
            ?->target;
    }    
}
