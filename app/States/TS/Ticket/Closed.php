<?php

namespace App\States\TS\Ticket;

 class Closed extends TicketState
{
    public static $name = "closed";

    public function label():string {
        return __('tickets/ticket.states.closed');
    }    
}