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
    public function setSubject(Ticket $ticket, Model $subject): TicketSubject|null
    {       
        $ticketSubject = $this->ticketSubject->where('ticket_id', '=', $ticket->id)->first();
        
        // update
        if ($ticketSubject !== null) {
            $ticketSubject->subject()->associate($subject);
            $ticketSubject->save();
        }
        // create
        else {
            $ticketSubject = new TicketSubject();
            $ticketSubject->ticket()->associate($ticket);
            $ticketSubject->subject()->associate($subject);
            $ticketSubject->save();
        }

        return $this->ticketSubject;
    }

    public function getSubject(Ticket $ticket): Model|null
    {        
        return $this->ticketSubject
            ->with('subject')
            ->where('ticket_id', '=', $ticket->id)
            ->first()
            ?->subject;
    }
}
