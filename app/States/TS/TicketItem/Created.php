<?php

namespace App\States\TS\TicketItem;

 class Created extends TicketItemState
{
    public static $name = "created";

    public function label():string {
        return __('tickets/ticket-item.states.created');
    }
}