<?php

namespace App\Services\Ticket;

use App\Models\TicketHeader;
use Dpb\Package\Tickets\Models\Ticket;

class HeaderService
{
    public function __construct(protected TicketHeader $ticketHeader) {}

    // public function assignUnitRate(ActivityTemplate $tempalte, UnitRate $unitRate)
    // {
    //     $this->erService->createRelation($ticket, $vehicle, 'assigned');
    // }

    public function setHeader(Ticket $ticket, TicketHeader $header): TicketHeader|null
    {       
        $ticketHeader = $this->ticketHeader->where('ticket_id', '=', $ticket->id)->first();
        
        // update
        if ($ticketHeader !== null) {
            $ticketHeader->ticket = $ticket;
            $ticketHeader->header = $header;
            // $ticketHeader->author = $this->auth->getAuthIdentifier();
            $ticketHeader->save();
        }
        else {
            $ticketHeader = new TicketHeader();
            $ticketHeader->ticket = $ticket;
            $ticketHeader->department = null;
            // $ticketHeader->author = $this->auth->getAuthIdentifier();
            $ticketHeader->save();

        }

        return $this->ticketHeader;
    }

    public function getHeader(Ticket $ticket): TicketHeader|null
    {        
        return $this->ticketHeader
            ->with(['department', 'author', 'assignedTo'])
            ->where('ticket_id', '=', $ticket->id)
            ->first();            
    }
}
