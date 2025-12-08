<?php

namespace App\UseCases\TicketAssignment;

use App\Data\Ticket\TicketAssignmentData;
use App\Data\Ticket\TicketData;
use App\Models\TicketAssignment;
use App\Services\TicketAssignmentService;
use Dpb\Package\Fleet\Models\MaintenanceGroup;
use Illuminate\Support\Carbon;
use App\States;
use Dpb\Package\Tickets\Models\TicketSource;
use Illuminate\Contracts\Auth\Guard;

class UpdateTicketAssignmentUseCase
{
    public function __construct(
        private TicketAssignmentService $ticketAssignmentSvc,
        private Guard $guard,
    ) {}

    public function execute(TicketAssignment $ticketAssignment, array $data): ?TicketAssignment
    {
        // ticket item
        $formTicketData = $data['ticket'];
        $ticketData = new TicketData(
            $ticketAssignment->ticket->id,
            Carbon::parse($formTicketData['date']),
            $formTicketData['title'] ?? null,
            $formTicketData['description'] ?? null,
            $formTicketData['group_id'],
            States\TS\Ticket\Created::$name,
        );
        // source TO DO
        $source = TicketSource::byCode('during-maintenance')->first();
        // assigned to TO DO
        $assignedTo = MaintenanceGroup::find($data['assigned_to_id'])?->first();
        dd($data, $assignedTo);

        // create ticket assignment
        $taData = new TicketAssignmentData(
            $ticketAssignment->id,
            $ticketData,
            $data['subject_id'],
            'vehicle',
            $source->id,
            $source->getMorphClass(),
            $this->guard->id(),
            $assignedTo !== null ? $assignedTo->id : null,
            $assignedTo !== null ? $assignedTo->getMorphClass() : null,
        );

        return $this->ticketAssignmentSvc->update($taData);
    }

//     public function execute1(TicketAssignment $ticketAssignment, array $data): ?TicketAssignment
//     {
//         // ticket
//         $ticketData = $data['ticket'];
//         $ticketAssignment->ticket->update([
//             'date' => $ticketData['date'],
//             'title' => $ticketData['title'] ?? null,
//             'description' => $ticketData['description'] ?? null,
//             'group_id' => $ticketData['group_id'],
//             // 'state' => $ticketData['state'],
//         ]);

//         // subject TO DO
//         $ticketAssignment->subject_id = $data['subject_id'];
//         // assigned to TO DO
//         $assignedTo = MaintenanceGroup::findSole($data['assigned_to_id']);
//         $ticketAssignment->assignedTo()->associate($assignedTo);

//         return $this->ticketAssignmentSvc->update($ticketAssignment);
// ;
//     }
}
