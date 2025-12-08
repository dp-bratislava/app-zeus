<?php

namespace App\Mappers\Ticket;

use App\Data\Ticket\TicketAssignmentData;
use App\Models\TicketAssignment;

// use Illuminate\Database\Eloquent\Collection;

class TicketAssignmentMapper
{

    public function __construct(
        private TicketAssignment $eloquentModel
    ) {}

    public function toEloquent(TicketAssignmentData $ticketAssignmentData): TicketAssignment
    {
        $model = $this->eloquentModel->firstOrNew(['id' => $ticketAssignmentData->id]);

        // dd($ticketAssignmentData->toArray());
        $model->fill($ticketAssignmentData->toArray());

        return $model;
    }
}
