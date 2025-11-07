<?php

namespace App\States\TS\Ticket;

 class Cancelled extends TicketState
{
    public static $name = "cancelled";

    public function label():string {
        return __('tickets/ticket.states.cancelled');
    }    
}