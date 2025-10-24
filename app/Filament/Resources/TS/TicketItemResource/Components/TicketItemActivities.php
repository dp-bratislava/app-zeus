<?php

namespace App\Filament\Resources\TS\TicketItemResource\Components;

use App\Models\ActivityAssignment;
use App\Services\Activity\Activity\WorkService;
use Dpb\Package\Tickets\Models\TicketItem;
use Livewire\Component;

class TicketItemActivities extends Component
{
    // public TicketItem $ticketItem;
    public $activities;
    public $totalExpectedDuration;
    public $totalDuration;
    public $workIntervals;
    public $workAssignments;

    // public function __construct(
    //     public TicketItem $ticketItem,
    //     public ActivityAssignment $activityAssignmentRepo,
    // ) {
    public function mount(
        TicketItem $ticketItem,
        ActivityAssignment $activityAssignmentRepo,
    ) {
        // $activitySvc = app(ActivityService::class);
        // $this->activities = $activitySvc->getActivities($this->ticketItem);
        // $this->totalExpectedDuration = $activitySvc->getTotalExpectedDuration($this->ticketItem);
        $this->activities = $activityAssignmentRepo->whereMorphedTo('subject', $ticketItem)
            ->with(['activity', 'activity.template'])
            ->get()
            ->map(fn($assignment) => $assignment->activity);

        // // work intervals
        // $this->workIntervals = [];
        $wiSvc = app(WorkService::class);
        // $this->totalDuration = 0;
        // foreach ($this->activities as $activity) {
        //     $this->workIntervals[$activity->id] = $wiSvc->getWorkIntervals($activity);            
        //     $this->totalDuration += $wiSvc->getTotalDuration($activity);
        // }

        // // work assignemnts
        $this->workAssignments = [];
        foreach ($this->activities as $activity) {
            $this->workAssignments[$activity->id] = $wiSvc->getWorkAssignments($activity);
        }
    }

    public function render()
    {
        return view('filament.resources.ts.ticket-item-resource.components.ticket-item-activities');
    }
}
