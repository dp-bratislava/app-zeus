<?php

namespace App\Services\Ticket;

use App\Models\ActivityAssignment;
use App\Models\TicketHeader;
use App\Models\TicketSubject;
use App\States\Ticket\Created;
use Dpb\Package\Activities\Models\Activity;
use Dpb\Package\Fleet\Models\Vehicle;
use Dpb\Package\Tickets\Models\Ticket;
use Illuminate\Contracts\Auth\Authenticatable;

class CreateTicketService
{
    public function __construct(
        protected HeaderService $headerSvc,
        protected SubjectService $subjectSvc,
        protected Authenticatable $auth
        // protected TicketHeader $ticketHeader,
        // protected TicketHeader $ticketHeader,
        // protected TicketHeader $ticketHeader,
    ) {}



    public function create($data): ?Ticket
    {
        // ticket
        $ticket = Ticket::create([
            'date' => $data['date'],
            'title' => $data['title'],
            'description' => $data['description'] ?? null,
            'source_id' => $data['source_id'],
            'state' => Created::$name
        ]);
        // ticket header
        $header = TicketHeader::create([
            'ticket_id' => $ticket->id,
            'department_id' => $data['department_id'] ?? null,
            'author_id' => $this->auth->getAuthIdentifier()
        ]);

        // ticket subject
        $this->subjectSvc->setSubject($ticket, Vehicle::find($data['subject_id']));

        // activities        
        if (isset($data['activities'])) {
            $activitiesData = $data['activities'];
            foreach ($activitiesData as $activityData) {
                $activity = Activity::create($activityData);
                $activityAssignment = new ActivityAssignment();
                $activityAssignment->activity()->associate($activity);
                $activityAssignment->subject()->associate($ticket);
                $activityAssignment->save();
            }
        }

        // services
        // $materials = $data['materials'];
        // materials
        // $services = $data['services'];

        return $ticket;
    }

    public function update($ticket, $data): ?Ticket
    {
        // ticket
        $ticket->update([
            'date' => $data['date'],
            'title' => $data['title'],
            'description' => $data['description'] ?? null,
            'source_id' => $data['source_id'],
            // 'state' => Created::$name
        ]);
        // ticket header
        $ticketHeader = $this->headerSvc->getHeader($ticket);
        $ticketHeader->update([
            'department_id' => $data['department_id'],
        ]);

        // ticket subject
        $this->subjectSvc->setSubject($ticket, Vehicle::find($data['subject_id']));

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
