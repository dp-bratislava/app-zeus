<?php

namespace App\States\TS\TicketItem;

 class AwaitingParts extends TicketItemState
{
    public static $name = "awaiting-parts";

    public function label():string {
        return __('tickets/ticket-item.states.awaiting-parts');
    }    
}