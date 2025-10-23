<?php

namespace App\States\TS\TicketItem;

 class InProgress extends TicketItemState
{
    public static $name = "in-progress";

    public function label():string {
        return __('tickets/ticket-item.states.in-progress');
    }    
}