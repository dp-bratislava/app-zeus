<?php

namespace App\Services;

use App\Data\Ticket\TicketAssignmentData;
use App\Models\TicketAssignment;
use App\Models\TicketItemAssignment;
use App\Repositories\TicketAssignmentRepository;
use Dpb\Package\Tickets\Models\TicketItem;
use Illuminate\Database\ConnectionInterface;

class TicketAssignmentService
{
    public function __construct(
        protected TicketAssignmentRepository $ticketAssignmentRepository,
    ) {}

    public function create(TicketAssignmentData $ticketAssignmentData): ?TicketAssignment
    {
        return $this->ticketAssignmentRepository->save($ticketAssignmentData);
    }

    public function update(TicketAssignmentData $ticketAssignmentData): ?TicketAssignment
    {
        return $this->ticketAssignmentRepository->save($ticketAssignmentData);
    }

    public function addTicketItem(array $payload): ?TicketItem
    {
        // to do 
        // dd($payload);        
//         create ticket item
        $ticketItem = TicketItem::create($payload['ticket_item']);

        // create ticket item assignent
        $tiData = $payload['ticket_item_assignment'];
        $tiData['ticket_item_id'] = $ticketItem->id;
        $tia = TicketItemAssignment::create($tiData);

        return $ticketItem;
        // return $this->ticketAssignmentRepository->save($ticketAssignment);
    }    
}
