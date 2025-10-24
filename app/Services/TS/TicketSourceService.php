<?php

namespace App\Services\TS;

use App\Data\ActivityData;
use App\Data\ActivityTemplateData;
use App\Data\MaterialData;
use App\Data\TicketData;
use App\Data\WorkIntervalData;
use App\Models\ActivityAssignment;
use App\Models\Expense\Material;
use App\Models\InspectionAssignment;
use App\Models\InspectionTemplateAssignment;
use App\Models\TicketAssignment;
use App\States\Inspection\InspectionState;
use Dpb\Package\Activities\Models\Activity;
use Dpb\Package\Activities\Models\ActivityTemplate;
use Dpb\Package\Inspections\Models\Inspection;
use Dpb\Package\Tickets\Models\Ticket;
use Illuminate\Support\Carbon;
use App\States;
use Dpb\Package\Tickets\Models\TicketItem;
use Illuminate\Contracts\Auth\Guard;
use Spatie\LaravelData\DataCollection;
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
