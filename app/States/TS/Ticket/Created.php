<?php

namespace App\States\TS\Ticket;

 class Created extends TicketState
{
    public static $name = "created";

    public function label():string {
        return __('tickets/ticket.states.created');
    }
}