<?php

namespace App\Services;

use App\Models\ActivityAssignment;
use App\Models\IncidentAssignment;
use App\Models\TicketAssignment;
use Dpb\Package\Activities\Models\Activity;
use Dpb\Package\Tickets\Models\Ticket;
use App\States;
use Carbon\Carbon;
use Dpb\Package\Fleet\Models\MaintenanceGroup;
use Dpb\Package\Fleet\Models\Vehicle;
use Dpb\Package\Incidents\Models\Incident;
use Dpb\Package\Tickets\Models\TicketGroup;
use Dpb\Package\Tickets\Models\TicketSource;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Database\ConnectionInterface;

// use Illuminate\Database\Eloquent\Collection;

class TicketAssignmentRepository
{
    protected const SUBJECT_TYPE = 'vehicle';

    public function __construct(
        protected ConnectionInterface $db,
        protected TicketAssignment $ticketAssignmentModel,
        protected IncidentRepository $incidentRepository,
        protected Guard $guard,
    ) {}

    public function createFromIncidentAssignment(IncidentAssignment $incidentAssignment)
    {
        $this->db->transaction(function () use ($incidentAssignment) {
            $date = Carbon::now();

            // create ticket
            $ticket = Ticket::create([
                'date' => $date,
                // 'title' => $incident->type->title . ' - nahlasene z dispec',
                'state' => States\TS\Ticket\Created::$name,
                'description' => $incidentAssignment->incident->description,
                'group_id' => TicketGroup::byCode($incidentAssignment->incident->type->code)->first()->id,
                'source_id' => TicketSource::byCode('in-service-dispatch')->first()->id
            ]);

            // create ticket items
            // $ticketItem = TicketItem::create([
            //     'date' => $date,
            //     'ticket_id' => $ticket->id,
            //     'title' => 'nahlasene z dispec',
            //     'description' => $incident->description,
            //     'state' => States\TS\TicketItem\Created::$name,
            // ]);

            // create ticket assignment
            $author = $this->guard->id();
            $assignedTo = $incidentAssignment->subject->maintenanceGroup;
            $ticketAssignment = $this->ticketAssignmentModel->newInstance();
            $ticketAssignment->ticket()->associate($ticket);
            $ticketAssignment->subject()->associate($incidentAssignment->subject);
            $ticketAssignment->source()->associate($incidentAssignment->incident);
            // $ticketAssignment->department()->associate($department);
            $ticketAssignment->author()->associate($author);
            $ticketAssignment->assignedTo()->associate($assignedTo);
            $ticketAssignment->save();

            return $ticketAssignment;
        });
    }

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
        if (isset($data['source']) && $data['source'] == 'incident') {
            $source = $this->incidentRepository->createFromTicket([
                'date' => $ticketData['date'],
                'description' => $ticketData['description'] ?? null,
                'ticket_group_id' => $ticketData['group_id'],
                'subject_id' => $data['subject_id']
            ]);
        }
        else {
            $source = TicketSource::byCode('during-maintenance')->first();
        }

        $author = $this->guard->id();
        $ticketAssignment = $this->ticketAssignmentModel->newInstance();
        $ticketAssignment->ticket()->associate($ticket);
        // subject TO DO
        $ticketAssignment->subject_id = $data['subject_id'];
        $ticketAssignment->subject_type = 'vehicle';
        // assigned to TO DO
        $assignedTo = MaintenanceGroup::findSole($data['assigned_to_id']);
        $ticketAssignment->assignedTo()->associate($assignedTo);

        $ticketAssignment->source()->associate($source);
        $ticketAssignment->author()->associate($author);
        $ticketAssignment->save();

        return $ticketAssignment;
    }

    public function update($ticketAssignment, $data): ?TicketAssignment
    {
        // ticket
        $ticketData = $data['ticket'];
        $ticketAssignment->ticket->update([
            'date' => $ticketData['date'],
            'title' => $ticketData['title'] ?? null,
            'description' => $ticketData['description'] ?? null,
            'group_id' => $ticketData['group_id'],
            // 'state' => $ticketData['state'],
        ]);

        // subject TO DO
        $ticketAssignment->subject_id = $data['subject_id'];
        // assigned to TO DO
        $assignedTo = MaintenanceGroup::findSole($data['assigned_to_id']);
        $ticketAssignment->assignedTo()->associate($assignedTo);
        $ticketAssignment->save();

        return $ticketAssignment;
    }
}
