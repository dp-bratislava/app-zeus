<?php

namespace App\StateTransitions\TS\Ticket;

use Dpb\Package\Tickets\Models\Ticket;
use App\States\TS\Ticket\Cancelled;
use App\States\TS\Ticket\InProgress;
use Illuminate\Contracts\Auth\Authenticatable;
use Spatie\ModelStates\Transition;

class InProgressToCancelled extends Transition
{
    public function __construct(private Ticket $ticket, private Authenticatable $user) {}

    public function canTransition(): bool
    {
        $userCan = app()->runningInConsole() ? true : ($this->user->can('cancel-ticket') || $this->user->hasRole('super-admin'));
        $validInitialState = $this->ticket->state->equals(InProgress::class);
        return $userCan && $validInitialState;
    }

    public function handle(): ?Ticket
    {
        if ($this->canTransition()) {

            $this->ticket->state = new Cancelled($this->ticket);
            $this->ticket->save();

            return $this->ticket;
        }
        return null;
    }
}
