<?php

namespace App\Services\Inspection;

use App\Models\InspectionAssignment;
use App\Services\Ticket\SubjectService;
use App\States;
use Dpb\Package\Inspections\Models\Inspection;
use Dpb\Package\Tickets\Models\Ticket;
use Illuminate\Database\ConnectionInterface;
use Illuminate\Support\Facades\DB;

class CreateTicketService
{
    public function __construct(
        protected ConnectionInterface $db,
        protected Ticket $ticket,
        protected SubjectService $subjectService,
        protected InspectionAssignment $inspectionAssignment
    ) {}

    public function createTicket(Inspection $inspection): Ticket|null
    {
        $this->db->transaction(function () use ($inspection) {
            // create main ticket based on inspection type
            $ticket = $this->ticket->create([
                'title' => $inspection->template->title,
                'date' => $inspection->date,
                'state' => States\Ticket\Created::$name,
            ]);

            // create ticket header
            // TO DO

            // create ticket subject
            $subject = $this->inspectionAssignment->where('inspection_id', '=', $inspection->id)->first()?->subject;
            if (($ticket !== null) && ($subject !== null)) {
                $this->subjectService->setSubject($ticket, $subject);
            }

            // create child tickets based on operations used on this inspection
            // TO DO

            return $ticket;
        });

        return null;
    }
}
