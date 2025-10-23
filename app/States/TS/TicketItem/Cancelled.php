<?php

namespace App\States\TS\TicketItem;

 class Cancelled extends TicketItemState
{
    public static $name = "cancelled";

    public function label():string {
        return __('tickets/ticket-item.states.cancelled');
    }    
}