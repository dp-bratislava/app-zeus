<?php

namespace App\Services\TS;

use App\Data\ActivityData;
use App\Data\ActivityTemplateData;
use App\Data\MaterialData;
use App\Data\TicketData;
use App\Data\WorkIntervalData;
use App\Models\ActivityAssignment;
use App\Models\DispatchReport;
use App\Models\Expense\Material;
use App\Models\IncidentAssignment;
use App\Models\InspectionAssignment;
use App\Models\InspectionTemplateAssignment;
use App\Models\TicketAssignment;
use App\Models\WorkAssignment;
use App\States\Inspection\InspectionState;
use Dpb\Package\Activities\Models\Activity;
use Dpb\Package\Activities\Models\ActivityTemplate;
use Dpb\Package\Inspections\Models\Inspection;
use Dpb\Package\Tickets\Models\Ticket;
use Illuminate\Support\Carbon;
use App\States;
use Dpb\Package\Fleet\Models\Vehicle;
use Dpb\Package\Incidents\Models\Incident;
use Dpb\Package\Inspections\Models\InspectionTemplate;
use Dpb\Package\Tickets\Models\TicketGroup;
use Dpb\Package\Tickets\Models\TicketItem;
use Dpb\Packages\WorkLog\Models\WorkInterval;
use Illuminate\Contracts\Auth\Guard;
use Spatie\LaravelData\DataCollection;
use Illuminate\Database\ConnectionInterface;

// use Illuminate\Database\Eloquent\Collection;

class TicketAssignmentService
{
    public function __construct(
        protected ConnectionInterface $db,
        protected Guard $guard,
        protected TicketAssignment $ticketAssignmentRepo,
        protected Vehicle $vehicleRepo,
        // protected SubjectService $subjectService,
        protected Activity $activityRepo,
        protected ActivityTemplate $activityTemplateRepo,
        protected ActivityAssignment $activityAssignmentRepo,
        protected Inspection $inspectionRepo,
        protected InspectionTemplate $inspectionTemplateRepo,
        protected InspectionAssignment $inspectionAssignmentRepo,
        protected WorkInterval $workIntervalRepo,
        protected WorkAssignment $workAssignmentRepo,
        // protected TicketCreateTicketService $createTicketService
    ) {}

    public function getSubject(Ticket $ticket)
    {
        return $this->ticketAssignmentRepo
            ->whereBelongsTo($ticket, 'ticket')
            ->first()
            ?->subject;
    }

    public function getSource(Ticket $ticket)
    {
        return $this->ticketAssignmentRepo
            ->whereBelongsTo($ticket, 'ticket')
            ->first()
            ?->source;
    }

    public function getSourceLabel(Ticket $ticket)
    {
        $sourceType = $this->ticketAssignmentRepo
            ->whereBelongsTo($ticket, 'ticket')
            ->first()
            ?->source_type;

        switch ($sourceType) {
            case 'inspection':
                return 'kontrola';
            case 'daily-maintenance':
                return 'denne osetrenie';
            case 'dispatch-report':
                return 'dispecing';
            default:
                return;
        }
    }

    private function createTicket($date, $title)
    {
        return Ticket::create([
            'date' => $date,
            'title' => $title,
            'state' => States\TS\Ticket\Created::$name,
        ]);
    }

    private function createTicketItem($ticket)
    {
        return TicketItem::create([
            'date' => $ticket->date,
            'ticket_id' => $ticket->id,
            'title' => $ticket->title,
            'state' => States\TS\TicketItem\Created::$name,
        ]);
    }

    public function createFromDailyMaintenance(array $formData)
    {
        if (empty($formData['vehicles'])) {
            return null;
        }

        $this->db->transaction(function () use ($formData) {
            $inspectionTemplate = $this->inspectionTemplateRepo->findOrFail($formData['inspection-template']);
            $date = $formData['date'];

            foreach ($formData['vehicles'] as $vehicleId) {

                // create inspection
                $inspection = $this->inspectionRepo->create([
                    'date' => $date,
                    'template_id' => $inspectionTemplate->id,
                    'state' => States\Inspection\Upcoming::$name,
                ]);

                // add inspection subject
                $inspectionAssignment = $this->inspectionAssignmentRepo->newInstance();
                $inspectionAssignment->inspection()->associate($inspection);
                $inspectionAssignment->subject()->associate($this->vehicleRepo->findOrFail($vehicleId));
                $inspectionAssignment->save();

                // create ticket
                $ticket = $this->createTicket($date, $inspectionTemplate->title);

                // create ticket items
                $ticketItem = $this->createTicketItem($ticket);

                // create activities from activity templates 
                // $activities = $this->createActivities($ticketItem, $formData['activity-templates']);

                // for each contract
                foreach ($formData['contracts'] as $contractId) {
                    // create activities from activity templates 
                    foreach ($formData['activity-templates'] as $activityTemplateId) {
                        $activity = $this->activityRepo->create([
                            'date' => $date,
                            'activity_template_id' => $activityTemplateId,
                        ]);

                        $activityAssignment = $this->activityAssignmentRepo->newInstance();
                        $activityAssignment->activity()->associate($activity);
                        $activityAssignment->subject()->associate($ticketItem);
                        $activityAssignment->save();

                        // add work log to activities
                        // create worktime
                        $duration = $activity->template->duration;
                        $workInterval = $this->workIntervalRepo->create([
                            'date' => $formData['date'],
                            'duration' => $duration
                        ]);

                        // assign worktime to contract and activity
                        $workAssignment = $this->workAssignmentRepo->newInstance();
                        $workAssignment->subject()->associate($activity);
                        $workAssignment->workInterval()->associate($workInterval);
                        $workAssignment->employeeContract()->associate($contractId);
                        $workAssignment->save();
                    }
                }

                // create ticket assignment
                $ticketSubject = InspectionAssignment::whereBelongsTo($inspection, $inspection->getMorphClass())
                    ->first()?->subject;
                $ticketAssignment = $this->createTicketAssignment($ticket, $inspection, $ticketSubject);
            }
        });
    }

    public function createFromIncident(Incident $incident)
    {
        $this->db->transaction(function () use ($incident) {
            $date = Carbon::now();

            // create ticket
            $ticket = Ticket::create([
                'date' => $date,
                // 'title' => $incident->type->title . ' - nahlasene z dispec',
                'state' => States\TS\Ticket\Created::$name,
                'description' => $incident->description,
                'group_id' => TicketGroup::byCode($incident->type->code)
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
            $ticketSubject = IncidentAssignment::whereBelongsTo($incident)->first()?->subject;
            $ticketAssignment = $this->createTicketAssignment($ticket, $incident, $ticketSubject);
        });
    }

    public function createFromDispatchReport(DispatchReport $dispatchReport)
    {
        $this->db->transaction(function () use ($dispatchReport) {
            $date = Carbon::now();

            // create ticket
            $ticket = Ticket::create([
                'date' => $date,
                'title' => 'nahlasene z dispec',
                'state' => States\TS\Ticket\Created::$name,
            ]);

            // create ticket items
            $ticketItem = TicketItem::create([
                'date' => $date,
                'ticket_id' => $ticket->id,
                'title' => 'nahlasene z dispec',
                'description' => $dispatchReport->description,
                'state' => States\TS\TicketItem\Created::$name,
            ]);

            // create ticket assignment
            $ticketSubject = $dispatchReport?->vehicle;
            $ticketAssignment = $this->createTicketAssignment($ticket, $dispatchReport, $ticketSubject);
        });
    }

    public function createFromInspection(Inspection $inspection)
    {
        $this->db->transaction(function () use ($inspection) {
            $date = Carbon::now();

            // create ticket
            $ticket = Ticket::create([
                'date' => $date,
                'title' => $inspection->template->title,
                'state' => States\TS\Ticket\Created::$name,
            ]);

            // create ticket items
            $ticketItem = TicketItem::create([
                'date' => $date,
                'ticket_id' => $ticket->id,
                'title' => $inspection->template->title,
                'state' => States\TS\TicketItem\Created::$name,
            ]);

            // create ticket item activities
            // get activity templates related to specified inspection template
            $activityTemplates = InspectionTemplateAssignment::whereBelongsTo($inspection->template, 'template')
                ->where('subject_type', $this->activityTemplateRepo->getMorphClass())
                ->with('subject')
                ->get()
                ->map(fn($assignment) => $assignment->subject);

            foreach ($activityTemplates as $key => $activityTemplate) {
                $activity = Activity::create([
                    'date' => $date,
                    'activity_template_id' => $activityTemplate->id,
                ]);

                ActivityAssignment::create([
                    'activity_id' => $activity->id,
                    'subject_id' => $ticket->id,
                    'subject_type' => $ticket->getMorphClass()
                ]);
            }

            // assign work
            // TO DO

            // create ticket assignment
            $ticketSubject = InspectionAssignment::whereBelongsTo($inspection, $inspection->getMorphClass())
                ->first()?->subject;
            $ticketAssignment = $this->createTicketAssignment($ticket, $inspection, $ticketSubject);
        });
    }

    protected function createTicketAssignment($ticket, $source, $ticketSubject): TicketAssignment
    {
        $author = $this->guard->id();
        $ticketAssignment = $this->ticketAssignmentRepo->newInstance();
        $ticketAssignment->ticket()->associate($ticket);
        $ticketAssignment->subject()->associate($ticketSubject);
        $ticketAssignment->source()->associate($source);
        // $ticketAssignment->department()->associate($department);
        $ticketAssignment->author()->associate($author);
        // $ticketAssignment->assignedTo()->associate($assignedTo);
        $ticketAssignment->save();

        return $ticketAssignment;
    }
    // public function createTicketFromInspection(Inspection $inspection): Returntype {}
}
