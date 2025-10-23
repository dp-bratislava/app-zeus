<?php

namespace App\StateTransitions\TS\TicketItem;

use App\States\TS\TicketItem\Cancelled;
use App\States\TS\TicketItem\InProgress;
use Dpb\Package\Tickets\Models\TicketItem;
use Illuminate\Contracts\Auth\Authenticatable;
use Spatie\ModelStates\Transition;

class InProgressToCancelled extends Transition
{
    public function __construct(private TicketItem $ticketItem, private Authenticatable $user) {}

    public function canTransition(): bool
    {
        $userCan = app()->runningInConsole() ? true : ($this->user->can('cancel-ticketItem') || $this->user->hasRole('super-admin'));
        $validInitialState = $this->ticketItem->state->equals(InProgress::class);
        return $userCan && $validInitialState;
    }

    public function handle(): ?TicketItem
    {
        if ($this->canTransition()) {

            $this->ticketItem->state = new Cancelled($this->ticketItem);
            $this->ticketItem->save();

            return $this->ticketItem;
        }
        return null;
    }
}
