<?php

namespace App\UseCases\TicketAssignment;

use App\Data\Ticket\TicketAssignmentData;
use App\Data\Ticket\TicketData;
use App\Models\InspectionAssignment;
use App\Models\TicketAssignment;
use App\Services\TicketAssignmentService;
use Illuminate\Contracts\Auth\Guard;
use App\States;
use Dpb\Package\Tickets\Models\TicketGroup;
use Illuminate\Support\Carbon;

class CreateFromInspectionUseCase
{
    public function __construct(
        private TicketAssignmentService $ticketAssignmentSvc,
        private TicketAssignment $ticketAssignmentModel,
        private Guard $guard,
    ) {}

    public function execute(InspectionAssignment $inspectionAssignment): ?TicketAssignment
    {
        // ticket item
        $date = Carbon::now();

        // create ticket
        $ticketData = new TicketData(
            null,
            $date,
            States\TS\Ticket\Created::$name,
            $inspectionAssignment->inspection->template->title,
            TicketGroup::byCode('inspection')->first()->id,
            States\TS\Ticket\Created::$name,
            // TicketSource::byCode('in-service-dispatch')->first()->id
        );

        // create ticket assignment
        $taData = new TicketAssignmentData(
            null,
            $ticketData,
            $inspectionAssignment->subject->id,
            $inspectionAssignment->subject->getMorphClass(),
            $inspectionAssignment->inspection->id,
            $inspectionAssignment->inspection->getMorphClass(),
            $this->guard->id(),
            $inspectionAssignment->subject->maintenanceGroup?->id,
            $inspectionAssignment->subject->maintenanceGroup?->getMorphClass(),
        );

        return $this->ticketAssignmentSvc->create($taData);
    }
}
