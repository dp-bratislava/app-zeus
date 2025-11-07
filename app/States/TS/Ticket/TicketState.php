<?php

namespace App\States\TS\Ticket;

use App\StateTransitions\TS\Ticket\CreatedToInProgress;
use App\StateTransitions\TS\Ticket\InProgressToCancelled;
use Dpb\Package\Tickets\States\TicketState as BaseTicketState;
use Spatie\ModelStates\StateConfig;

abstract class TicketState extends BaseTicketState
{
    public static function config(): StateConfig
    {
        return parent::config()
            ->default(Created::class)
            ->allowTransition(Created::class, InProgress::class, CreatedToInProgress::class)
            ->allowTransition(InProgress::class, Closed::class)
            ->allowTransition(InProgress::class, Cancelled::class, InProgressToCancelled::class)
        ;
    }
}
