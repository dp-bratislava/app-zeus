<?php

namespace App\Services;

use App\Models\ActivityAssignment;
use App\Models\TicketAssignment;
use Dpb\Package\Activities\Models\Activity;
use Dpb\Package\Tickets\Models\Ticket;
use App\States;
use Dpb\Package\Fleet\Models\Vehicle;
use Illuminate\Contracts\Auth\Guard;

// use Illuminate\Database\Eloquent\Collection;

class TicketRepository
{
    public function __construct(
        protected Guard $guard,
        protected TicketAssignment $ticketAssignmentRepo,
    ) {}

    public function create(array $data): ?Ticket
    {
        // ticket item
        $ticket = Ticket::create([
            'date' => $data['date'],
            'title' => $data['title'] ?? null,
            'description' => $data['description'] ?? null,
            'group_id' => $data['group_id'],
            'state' => States\TS\Ticket\Created::$name,
            // 'state' => $data['state']
        ]);


        // ticket items activities
        if (isset($data['activities'])) {
            $activitiesData = $data['activities'];
            foreach ($activitiesData as $activityData) {
                $activity = Activity::create($activityData);
                $activityAssignment = new ActivityAssignment();
                $activityAssignment->activity()->associate($activity);
                $activityAssignment->subject()->associate($ticket);
                $activityAssignment->save();

                // ticket item activities work        
                if (isset($activitiesData['workAssignments'])) {
                    $workAssignmentsData = $activitiesData['workAssignments'];
                    foreach ($workAssignmentsData as $workAssignmentData) {
                        $workInterval = WorkInterval::create(attributes: $workAssignmentData);
                        dd($workInterval);
                        $workAssignment = new WorkAssignment();
                        $workAssignment->workInterval()->associate($workInterval);
                        $workAssignment->subject()->associate($activity);
                        $workAssignment->employeeContract()->associate($workAssignmentData['employee_contract_id']);
                        $workAssignment->save();
                    }
                }
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
            'title' => $data['title'] ?? null,
            'description' => $data['description'] ?? null,
            // 'state' => Created::$name
        ]);

        // ticket assignmet
        $author = $this->guard->id();
        $ticketSubject = Vehicle::find($data['subject_id']);
        $ticketAssignment = $this->ticketAssignmentRepo->whereBelongsTo($ticket, 'ticket')->first();
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
