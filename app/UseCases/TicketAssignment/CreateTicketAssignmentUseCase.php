<?php

namespace App\UseCases\TicketAssignment;

use App\Data\Ticket\TicketAssignmentData;
use App\Data\Ticket\TicketData;
use App\Models\TicketAssignment;
use App\Services\TicketAssignmentService;
use Dpb\Package\Fleet\Models\MaintenanceGroup;
use Dpb\Package\Tickets\Models\Ticket;
use Dpb\Package\Tickets\Models\TicketSource;
use Illuminate\Contracts\Auth\Guard;
use App\States;
use Illuminate\Support\Carbon;

class CreateTicketAssignmentUseCase
{
    public function __construct(
        private TicketAssignmentService $ticketAssignmentSvc,
        // private TicketAssignment $ticketAssignmentModel,
        private Guard $guard,
    ) {}

    public function execute(array $data): ?TicketAssignment
    {
        // ticket item
        $formTicketData = $data['ticket'];
        $ticketData = new TicketData(
            null,
            Carbon::parse($formTicketData['date']),
            $formTicketData['title'] ?? null,
            $formTicketData['description'] ?? null,
            $formTicketData['group_id'],
            States\TS\Ticket\Created::$name,
        );
        
        // source TO DO
        $source = TicketSource::byCode('during-maintenance')->first();
        // assigned to TO DO
        $assignedTo = MaintenanceGroup::find($data['assigned_to_id'])?->first();

        // create ticket assignment
        $taData = new TicketAssignmentData(
            null,
            $ticketData,
            $data['subject_id'],
            'vehicle',
            $source->id,
            $source->getMorphClass(),
            $this->guard->id(),
            $assignedTo !== null ? $assignedTo->id : null,
            $assignedTo !== null ? $assignedTo->getMorphClass() : null,
        );

        return $this->ticketAssignmentSvc->create($taData);
    }

    // public function execute1(array $data): ?TicketAssignment
    // {
    //     // ticket item
    //     $ticketData = $data['ticket'];
    //     $ticket = Ticket::create([
    //         'date' => $ticketData['date'],
    //         'title' => $ticketData['title'] ?? null,
    //         'description' => $ticketData['description'] ?? null,
    //         'group_id' => $ticketData['group_id'],
    //         'state' => States\TS\Ticket\Created::$name,
    //         // 'state' => $data['state']
    //     ]);

    //     // source TO DO
    //     $source = null;
    //     $source = TicketSource::byCode('during-maintenance')->first();

    //     $author = $this->guard->id();
    //     $ticketAssignment = $this->ticketAssignmentModel->newInstance();
    //     $ticketAssignment->ticket()->associate($ticket);
    //     // subject TO DO
    //     $ticketAssignment->subject_id = $data['subject_id'];
    //     $ticketAssignment->subject_type = 'vehicle';
    //     // assigned to TO DO
    //     $assignedTo = MaintenanceGroup::findSole($data['assigned_to_id']);
    //     $ticketAssignment->assignedTo()->associate($assignedTo);

    //     $ticketAssignment->source()->associate($source);
    //     $ticketAssignment->author()->associate($author);

    //     return $this->ticketAssignmentSvc->create($ticketAssignment);
    // }
}
