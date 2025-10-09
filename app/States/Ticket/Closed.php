<?php

namespace App\States\Ticket;

 class Closed extends TicketState
{
    public static $name = "closed";

    public function label():string {
        return __('tickets/ticket.states.closed');
    }    
}