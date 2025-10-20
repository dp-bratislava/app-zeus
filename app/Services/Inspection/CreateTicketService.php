<?php

namespace App\Services\Inspection;

use App\Models\Datahub\Department;
use App\Models\InspectionAssignment;
use App\Models\InspectionTemplateAssignment;
use App\Services\Ticket\ActivityService;
use App\Services\Ticket\CreateTicketService as TicketCreateTicketService;
use App\Services\Ticket\SubjectService;
use App\States;
use Dpb\Package\Activities\Models\Activity;
use Dpb\Package\Inspections\Models\Inspection;
use Dpb\Package\Tickets\Models\Ticket;
use Dpb\Package\Tickets\Models\TicketSource;
use Illuminate\Database\ConnectionInterface;

class CreateTicketService
{
    public function __construct(
        protected ConnectionInterface $db,
        protected Ticket $ticket,
        protected SubjectService $subjectService,
        protected ActivityService $activitySvc,
        protected InspectionAssignment $inspectionAssignment,
        protected TicketCreateTicketService $createTicketService
    ) {}

    public function createTicket(Inspection $inspection): Ticket|null
    {
        $this->db->transaction(function () use ($inspection) {
            // create main ticket based on inspection type
            $subjectId = $this->inspectionAssignment
                ->where('inspection_id', '=', $inspection->id)
                ->first()?->subject->id;

            $data = [
                'date' => $inspection->date,
                'title' => $inspection->template->title,
                'source_id' => TicketSource::byCode('planned-maintenance')->first()->id,
                States\Ticket\Created::$name,
                'department_id' => Department::where('code', '=', '9800')->first()->id,
                'subject_id' => $subjectId
            ];

            $ticket = $this->createTicketService->create($data);

            $activityTempaltes = InspectionTemplateAssignment::where('template_id', $inspection->template->id)
                ->where('subject_type', 'activity-template')
                ->with('subject')
                ->get()
                ->map(fn($assignment) => $assignment->subject);

            // create activities from templates 
            $activities = [];
            foreach ($activityTempaltes as $key => $activityTempalte) {
                $activities[] = Activity::create([
                    'date' => $inspection->date,
                    'activity_template_id' => $activityTempalte->id,
                ]);
            }

            $this->activitySvc->addActivities($ticket, collect($activities));
            return $ticket;
        });

        return null;
    }

    public function createTicket1(Inspection $inspection): Ticket|null
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
