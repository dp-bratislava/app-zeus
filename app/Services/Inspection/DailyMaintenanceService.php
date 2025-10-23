<?php

namespace App\Services\Inspection;

use App\Models\Datahub\Department;
use App\Models\InspectionAssignment;
use App\Models\InspectionTemplateAssignment;
use App\Models\WorkAssignment;
use App\Services\TS\ActivityService;
use App\Services\TS\CreateTicketService as TicketCreateTicketService;
use App\Services\TS\SubjectService;
use App\States;
use Carbon\Carbon;
use Dpb\Package\Activities\Models\Activity;
use Dpb\Package\Activities\Models\ActivityTemplate;
use Dpb\Package\Fleet\Models\Vehicle;
use Dpb\Package\Inspections\Models\Inspection;
use Dpb\Package\Inspections\Models\InspectionTemplate;
use Dpb\Package\Tickets\Models\Ticket;
use Dpb\Package\Tickets\Models\TicketSource;
use Dpb\Packages\WorkLog\Models\WorkInterval;
use Illuminate\Database\ConnectionInterface;

class DailyMaintenanceService
{
    public function __construct(
        protected ConnectionInterface $db,
        protected Ticket $ticket,
        protected Vehicle $vehicleRepo,
        protected Activity $activityRepo,
        protected ActivityTemplate $activityTemplateRepo,
        protected InspectionTemplate $inspectionTemplateRepo,
        protected SubjectService $subjectSvc,
        protected ActivityService $activitySvc,
        protected InspectionAssignment $inspectionAssignment,
        protected CreateInspectionService $createInspectionSvc,
        protected TicketCreateTicketService $createTicketSvc,
        protected WorkAssignment $workAssignmentRepo,
        protected WorkInterval $workIntervalRepo
    ) {}

    public function create(array $formData)
    {
        if (empty($formData['vehicles'])) {
            return null;
        }

        $this->db->transaction(function () use ($formData) {
            $inspectionTemplate = $this->inspectionTemplateRepo->findOrFail($formData['inspection-template']);
            $date = $formData['date'];

            foreach ($formData['vehicles'] as $vehicleId) {
                // create inspection
                $inspection = $this->createInspectionSvc->create($this->vehicleRepo->findOrFail($vehicleId), $inspectionTemplate);

                // create ticket with header and subject
                $ticketData = [
                    'date' => $formData['date'],
                    'title' => $inspectionTemplate->title,
                    'source_id' => TicketSource::byCode('planned-maintenance')->first()->id,
                    States\TS\Ticket\Created::$name,
                    'department_id' => Department::where('code', '=', '9800')->first()->id,
                    'subject_id' => $vehicleId
                ];

                $ticket = $this->createTicketSvc->create($ticketData);

                // create activities from activity templates 
                $activities = [];
                foreach ($formData['activity-templates'] as $activityTempalteId) {
                    $activities[] = $this->activityRepo->create([
                        'date' => $formData['date'],
                        'activity_template_id' => $activityTempalteId,
                    ]);
                }

                $this->activitySvc->addActivities($ticket, collect($activities));

                // add work log to activities
                foreach ($activities as $activity) {
                    $duration = $activity->template->duration;

                    // for each contract
                    foreach ($formData['contracts'] as $contractId) {
                        // create worktime
                        $workInterval = $this->workIntervalRepo->create([
                            'date' => $formData['date'],
                            'duration' => $duration
                        ]);

                        // assign worktime to contract and activity
                        $workAssignment = new $this->workAssignmentRepo();
                        $workAssignment->subject()->associate($activity);
                        $workAssignment->workInterval()->associate($workInterval);
                        $workAssignment->employeeContract()->associate($contractId);
                        $workAssignment->save();
                    }
                }
            }
        });
    }
}
