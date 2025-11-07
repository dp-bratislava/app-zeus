<?php

namespace App\Services;

use App\Models\ActivityAssignment;
use App\Models\TicketAssignment;
use Dpb\Package\Activities\Models\Activity;
use Dpb\Package\Tickets\Models\Ticket;
use App\States;
use Dpb\Package\Fleet\Models\Vehicle;
use Dpb\Package\Incidents\Models\Incident;
use Illuminate\Contracts\Auth\Guard;

// use Illuminate\Database\Eloquent\Collection;

class TicketAssignmentRepository
{
    protected const SUBJECT_TYPE = 'vehicle';

    public function __construct(
        protected TicketAssignment $ticketAssignmentModel,
        protected IncidentRepository $incidentRepository,
        protected Guard $guard,
    ) {}

    public function create(array $data): ?TicketAssignment
    {
        // ticket item
        $ticketData = $data['ticket'];
        $ticket = Ticket::create([
            'date' => $ticketData['date'],
            'title' => $ticketData['title'] ?? null,
            'description' => $ticketData['description'] ?? null,
            'group_id' => $ticketData['group_id'],
            'state' => States\TS\Ticket\Created::$name,
            // 'state' => $data['state']
        ]);

        // source TO DO
        $source = null;
        if ($data['source'] == 'incident') {
            $source = $this->incidentRepository->createFromTicket([
                'date' => $ticketData['date'],
                'description' => $ticketData['description'] ?? null,
                'ticket_group_id' => $ticketData['group_id'],
                'subject_id' => $data['subject_id']
            ]);
        }

        $author = $this->guard->id();
        $ticketAssignment = $this->ticketAssignmentModel->newInstance();
        $ticketAssignment->ticket()->associate($ticket);
        // subject TO DO
        $ticketAssignment->subject_id = $data['subject_id'];
        $ticketAssignment->subject_type = 'vehicle';

        $ticketAssignment->source()->associate($source);
        $ticketAssignment->author()->associate($author);
        $ticketAssignment->save();

        return $ticketAssignment;
    }

    public function update($ticket, $data): ?Ticket
    {
        // ticket
        $ticket->update([
            'date' => $data['date'],
            'title' => $data['title'] ?? null,
            'description' => $data['description'] ?? null,
            // 'state' => Created::$name
        ]);

        // ticket assignmet
        $author = $this->guard->id();
        $ticketSubject = Vehicle::find($data['subject_id']);
        $ticketAssignment = $this->ticketAssignmentModel->whereBelongsTo($ticket, 'ticket')->first();
        // dd($ticketAssignment);
        // $ticketAssignment->ticket()->associate($ticket);
        $ticketAssignment->subject()->associate($ticketSubject);
        // $ticketAssignment->source()->associate($source);
        // $ticketAssignment->department()->associate($department);
        // $ticketAssignment->author()->associate($author);
        // $ticketAssignment->assignedTo()->associate($assignedTo);
        $ticketAssignment->save();

        // $this->subjectSvc->setSubject($ticket, Vehicle::find($data['subject_id']));

        // activities        
        // if (isset($data['activities'])) {
        //     $activitiesData = $data['activities'];
        //     foreach ($activitiesData as $activityData) {
        //         $activity = Activity::create($activityData);
        //         $activityAssignment = new ActivityAssignment();
        //         $activityAssignment->activity()->associate($activity);
        //         $activityAssignment->subject()->associate($ticket);
        //         $activityAssignment->save();
        //     }
        // }

        // services
        // $materials = $data['materials'];
        // materials
        // $services = $data['services'];

        return $ticket;
    }
}
