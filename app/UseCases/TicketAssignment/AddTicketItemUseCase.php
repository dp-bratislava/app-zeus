<?php

namespace App\UseCases\TicketAssignment;

use App\Services\Ticket\TicketAssignmentService;
use App\Repositories\Ticket\TicketAssignmentRepository;
use Dpb\Package\Fleet\Models\MaintenanceGroup;
use Dpb\Package\Tickets\Models\TicketItem;
use Illuminate\Contracts\Auth\Guard;

class AddTicketItemUseCase
{
    public function __construct(
        private TicketAssignmentService $ticketAssignmentSvc,
        private TicketAssignmentRepository $ticketAssignmentRepo,
        protected Guard $guard,
    ) {}

    public function execute(int $ticketAssignmentId, array $data): ?TicketItem
    {
        // get ticket assignemtn 
        $ta = $this->ticketAssignmentRepo->findById($ticketAssignmentId);

        $ta->ticket_id;
        $ticketItemData = [
            'ticket_id' => $ta->ticket_id ?? null,
            'date' => $data['date'],
            'title' => $data['title'] ?? null,
            'description' => $data['description'] ?? null,
            'group_id' => $data['group_id'] ?? null,
            'state' => $data['state'] ?? null
        ];

        // ticket item assignment
        $author = $this->guard->id();
        $assignedTo = MaintenanceGroup::findSole($data['assigned_to']) ?? null;

        $ticketItemAssignmentData = [
            'assigned_to_id' => $data['assigned_to'] ?? null,
            'assigned_to_type' => $assignedTo?->getMorphClass() ?? null,
            'author_id' => $author
        ];       
        
        $payload = [
            'ticket_item' => $ticketItemData,
            'ticket_item_assignment' => $ticketItemAssignmentData
        ];

        return $this->ticketAssignmentSvc->addTicketItem($payload);
    }
}
