<?php

namespace App\Services;

use App\Data\ActivityData;
use App\Data\ActivityTemplateData;
use App\Data\MaterialData;
use App\Data\TicketData;
use App\Data\WorkIntervalData;
use App\Models\ActivityAssignment;
use App\Models\Expense\Material;
use App\Models\TicketAssignment;
use App\Models\TicketItemAssignment;
use App\Models\WorkAssignment;
use Dpb\Package\Activities\Models\Activity;
use Dpb\Package\Activities\Models\ActivityTemplate;
use Dpb\Package\Fleet\Models\MaintenanceGroup;
use Dpb\Package\Tickets\Models\Ticket;
use Dpb\Package\Tickets\Models\TicketItem;
use Dpb\Packages\WorkLog\Models\WorkInterval;
use Illuminate\Contracts\Auth\Guard;
use Spatie\LaravelData\DataCollection;

// use Illuminate\Database\Eloquent\Collection;

class TicketItemRepository
{
    public function __construct(
        protected Guard $guard,
        protected TicketAssignment $ticketAssignmentRepo,
        protected TicketItemAssignment $ticketItemAssignmentRepo,
        protected ActivityAssignmentRepository $activityAssignmentRepo,
    ) {}

    public function create(array $data): ?TicketItem
    {
        // dd($data);
        // ticket item
        $ticketItem = TicketItem::create([
            'ticket_id' => $data['ticket_id'] ?? null,
            'date' => $data['date'],
            'title' => $data['title'] ?? null,
            'description' => $data['description'] ?? null,
            'group_id' => $data['group_id'] ?? null,
            'state' => $data['state'] ?? null
        ]);

        // ticket item assignment
        if (isset($data['assigned_to'])) {
            $author = $this->guard->id();
            $assignedTo = MaintenanceGroup::find($data['assigned_to']);
            $tiAssignmet = $this->ticketItemAssignmentRepo->newInstance();
            $tiAssignmet->ticketItem()->associate($ticketItem);
            $tiAssignmet->assignedTo()->associate($assignedTo);
            $tiAssignmet->author()->associate($author);
            $tiAssignmet->save();
            // dd($tiAssignmet);
        }

        // ticket item activities
        // if (isset($data['activities'])) {
        //     $activitiesData = $data['activities'];
        //     foreach ($activitiesData as $activityData) {
        //         $activity = Activity::create($activityData);
        //         $activityAssignment = new ActivityAssignment();
        //         $activityAssignment->activity()->associate($activity);
        //         $activityAssignment->subject()->associate($ticketItem);
        //         $activityAssignment->save();

        //         // ticket item activities work        
        //         if (isset($activitiesData['workAssignments'])) {
        //             $workAssignmentsData = $activitiesData['workAssignments'];
        //             foreach ($workAssignmentsData as $workAssignmentData) {
        //                 $workInterval = WorkInterval::create(attributes: $workAssignmentData);
        //                 dd($workInterval);
        //                 $workAssignment = new WorkAssignment();
        //                 $workAssignment->workInterval()->associate($workInterval);
        //                 $workAssignment->subject()->associate($activity);
        //                 $workAssignment->employeeContract()->associate($workAssignmentData['employee_contract_id']);
        //                 $workAssignment->save();
        //             }
        //         }
        //     }
        // }


        // services
        // $materials = $data['materials'];
        // materials
        // $services = $data['services'];

        return $ticketItem;
    }

    public function update(TicketItem $ticketItem, array $data): ?TicketItem
    {
        // ticket item
        $ticketItem->update([
            'date' => $data['date'],
            'title' => $data['title'] ?? null,
            'description' => $data['description'] ?? null,
            'group_id' => $data['group_id'],
            'state' => $data['state'] ?? null
        ]);

        // ticket item assignment
        if (isset($data['assigned_to'])) {
            $author = $this->guard->id();
            $assignedTo = MaintenanceGroup::findSole($data['assigned_to']);
            // dd($ticketItem);
            $tiAssignmet = $this->ticketItemAssignmentRepo->whereBelongsTo($ticketItem, 'ticketItem')->first();
            // dd($tiAssignmet);
            $tiAssignmet->assignedTo()->associate($assignedTo);
            $tiAssignmet->save();
        }

        // ticket item activities
        if (isset($data['activities'])) {
            $this->activityAssignmentRepo->syncActivities($ticketItem, $data['activities']);
        }
        // if (isset($data['activities'])) {
        //     $activitiesData = $data['activities'];
        //     foreach ($activitiesData as $activityData) {
        //         $activity = Activity::create($activityData);
        //         $activityAssignment = new ActivityAssignment();
        //         $activityAssignment->activity()->associate($activity);
        //         $activityAssignment->subject()->associate($ticketItem);
        //         $activityAssignment->save();

        //         // ticket item activities work        
        //         if (isset($activitiesData['workAssignments'])) {
        //             $workAssignmentsData = $activitiesData['workAssignments'];
        //             foreach ($workAssignmentsData as $workAssignmentData) {
        //                 $workInterval = WorkInterval::create(attributes: $workAssignmentData);
        //                 $workAssignment = new WorkAssignment();
        //                 $workAssignment->workInterval()->associate($workInterval);
        //                 $workAssignment->subject()->associate($activity);
        //                 $workAssignment->employeeContract()->associate($workAssignmentData['employee_contract_id']);
        //                 $workAssignment->save();
        //             }
        //         }
        //     }
        // }


        // services
        // $materials = $data['materials'];
        // materials
        // $services = $data['services'];

        return $ticketItem;
    }
}
