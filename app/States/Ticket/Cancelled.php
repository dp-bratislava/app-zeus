<?php

namespace App\States\Ticket;

 class Cancelled extends TicketState
{
    public static $name = "cancelled";

    public function label():string {
        return __('tickets/ticket.states.cancelled');
    }    
}