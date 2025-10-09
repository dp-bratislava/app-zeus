<?php

namespace App\States\Ticket;

 class InProgress extends TicketState
{
    public static $name = "in_progress";

    public function label():string {
        return __('tickets/ticket.states.in_progress');
    }    
}