<?php

namespace App\Mappers\Ticket;

use App\Data\Ticket\TicketData;
use Dpb\Package\Tickets\Models\Ticket;

// use Illuminate\Database\Eloquent\Collection;

class TicketMapper
{

    public function __construct(
        private Ticket $eloquentModel
    ) {}

    public function toEloquent(TicketData $ticketData): Ticket
    {
        $model = $this->eloquentModel->firstOrNew(['id' => $ticketData->id]);
        $model->fill($ticketData->toArray());
        // $model->date = $ticket->date;
        // $model->title = $ticket->title;
        // $model->description = $ticket->description;
        // $model->group_id = $ticket->groupId;

        // $model->sate = $task->state();
        // map items 
        // $model->items = collect($task->taskItems())
        //     ->map(fn($item) => $this->tiMapper->toEloquent($item))
        //     ->toArray();

        return $model;
    }
}
