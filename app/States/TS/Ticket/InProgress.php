<?php

namespace App\States\TS\Ticket;

 class InProgress extends TicketState
{
    public static $name = "in-progress";

    public function label():string {
        return __('tickets/ticket.states.in-progress');
    }    
}