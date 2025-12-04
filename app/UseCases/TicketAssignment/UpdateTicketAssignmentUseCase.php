<?php

namespace App\UseCases\TicketAssignment;

use App\Models\TicketAssignment;
use App\Services\Ticket\TicketAssignmentService;
use Dpb\Package\Fleet\Models\MaintenanceGroup;

class UpdateTicketAssignmentUseCase
{
    public function __construct(
        private TicketAssignmentService $ticketAssignmentSvc,
    ) {}

    public function execute(TicketAssignment $ticketAssignment, array $data): ?TicketAssignment
    {
        // ticket
        $ticketData = $data['ticket'];
        $ticketAssignment->ticket->update([
            'date' => $ticketData['date'],
            'title' => $ticketData['title'] ?? null,
            'description' => $ticketData['description'] ?? null,
            'group_id' => $ticketData['group_id'],
            // 'state' => $ticketData['state'],
        ]);

        // subject TO DO
        $ticketAssignment->subject_id = $data['subject_id'];
        // assigned to TO DO
        $assignedTo = MaintenanceGroup::findSole($data['assigned_to_id']);
        $ticketAssignment->assignedTo()->associate($assignedTo);

        return $this->ticketAssignmentSvc->update($ticketAssignment);
;
    }
}
