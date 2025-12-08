<?php

namespace App\Repositories;

use App\Data\Ticket\TicketAssignmentData;
use App\Mappers\Ticket\TicketAssignmentMapper;
use App\Mappers\Ticket\TicketMapper;
use App\Models\TicketAssignment;

class TicketAssignmentRepository
{
    public function __construct(
        private TicketAssignment $eloquentModel,
        private TicketMapper $ticketMapper,
        private TicketAssignmentMapper $ticketAssignmentMapper
        ) {}

    public function findById(int $id): ?TicketAssignment
    {
        $model = $this->eloquentModel->findOrFail($id);

        return $model;
    }

    public function save(TicketAssignmentData $ticketAssignmentData): ?TicketAssignment
    {
        // create ticket
        $ticket = $this->ticketMapper->toEloquent($ticketAssignmentData->ticket);
        $ticket->save();

        $ticketAssignment = $this->ticketAssignmentMapper->toEloquent($ticketAssignmentData);
        // dd($ticketAssignment);
        $ticketAssignment->ticket()->associate($ticket);
        $ticketAssignment->save();

        return $ticketAssignment;
    }

    public function push(TicketAssignment $ticketAssignment): ?TicketAssignment
    {
        $ticketAssignment->push();
        return $ticketAssignment;
    }    
}
