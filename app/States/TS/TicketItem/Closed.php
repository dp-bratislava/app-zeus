<?php

namespace App\States\TS\TicketItem;

 class Closed extends TicketItemState
{
    public static $name = "closed";

    public function label():string {
        return __('tickets/ticket-item.states.closed');
    }    
}