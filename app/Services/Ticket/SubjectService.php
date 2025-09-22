<?php

namespace App\Services\Ticket;

use App\Models\TicketSubject;
use Dpb\Package\Tickets\Models\Ticket;
use Illuminate\Database\Eloquent\Model;

class SubjectService
{
    public function __construct(protected TicketSubject $ticketSubject) {}

    // public function assignUnitRate(ActivityTemplate $tempalte, UnitRate $unitRate)
    // {
    //     $this->erService->createRelation($ticket, $vehicle, 'assigned');
    // }

    public function getSubject(Ticket $ticket): Model|null
    {        
        return $this->ticketSubject
            ->with('subject')
            ->where('ticket_id', '=', $ticket->id)
            ->first()
            ?->subject;
    }
}
