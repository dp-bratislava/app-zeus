<?php

namespace App\UseCases\TicketAssignment;

use App\Data\Ticket\TicketAssignmentData;
use App\Data\Ticket\TicketData;
use App\Models\IncidentAssignment;
use App\Models\TicketAssignment;
use App\Services\Ticket\TicketAssignmentService;
use Dpb\Package\Fleet\Models\MaintenanceGroup;
use Dpb\Package\Tickets\Models\Ticket;
use Dpb\Package\Tickets\Models\TicketSource;
use Illuminate\Contracts\Auth\Guard;
use App\States;
use Dpb\Package\Tickets\Models\TicketGroup;
use Illuminate\Support\Carbon;

class CreateFromIncidentUseCase
{
    public function __construct(
        private TicketAssignmentService $ticketAssignmentSvc,
        private TicketAssignment $ticketAssignmentModel,
        private Guard $guard,
    ) {}

    public function execute(IncidentAssignment $incidentAssignment): ?TicketAssignment
    {
        // ticket item
            $date = Carbon::now();

            // create ticket
            $ticketData = new TicketData(
                null,
                $date,                
                States\TS\Ticket\Created::$name,
                $incidentAssignment->incident->description,
                TicketGroup::byCode($incidentAssignment->incident->type->code)->first()->id,
                TicketSource::byCode('in-service-dispatch')->first()->id
            );

            // create ticket assignment
            $taData = new TicketAssignmentData(
                null,
                $ticketData,
                $incidentAssignment->subject->id,
                $incidentAssignment->subject->getMorphClass(),
                $incidentAssignment->incident->id,
                $incidentAssignment->incident->getMorphClass(),
                $this->guard->id(),
                $incidentAssignment->subject->maintenanceGroup->id,
                $incidentAssignment->subject->maintenanceGroup->getMorphClass(),
           );

        return $this->ticketAssignmentSvc->create($taData);
    }
}
