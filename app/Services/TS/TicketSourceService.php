<?php

namespace App\Services\TS;

use App\Models\InspectionAssignment;
use App\Models\TicketAssignment;
use Dpb\Package\Activities\Models\ActivityTemplate;
use Dpb\Package\Tickets\Models\Ticket;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Database\ConnectionInterface;

// use Illuminate\Database\Eloquent\Collection;

class TicketSourceService
{
    public function __construct(
        protected ConnectionInterface $db,
        protected Guard $guard,
        protected TicketAssignment $ticketAssignment,
        // protected Ticket $ticket,
        // protected SubjectService $subjectService,
        protected ActivityTemplate $activityTemplateRepo,
        // protected InspectionAssignment $inspectionAssignment,
        // protected TicketCreateTicketService $createTicketService
    ) {}

    public function getSource(Ticket $ticket)
    {
        // $ticket->source->title
    }
}
