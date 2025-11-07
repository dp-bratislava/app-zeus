<?php

namespace App\States\TS\TicketItem;

use App\StateTransitions\TS\TicketItem\CreatedToInProgress;
use App\StateTransitions\TS\TicketItem\InProgressToCancelled;
use Dpb\Package\Tickets\States\TicketItemState as BaseTicketItemState;
use Spatie\ModelStates\StateConfig;

abstract class TicketItemState extends BaseTicketItemState
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
