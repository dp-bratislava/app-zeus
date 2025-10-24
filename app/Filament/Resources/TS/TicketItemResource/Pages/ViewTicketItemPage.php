<?php

namespace App\Filament\Resources\TS\TicketItemResource\Pages;

use App\Filament\Resources\TS\TicketItemResource;
use App\Models\ActivityAssignment;
use App\Models\TicketAssignment;
use App\Models\TicketHeader;
use App\Models\TicketSubject;
use App\Services\Activity\Activity\WorkService;
use App\Services\TS\ActivityService;
use App\Services\TS\HeaderService;
use App\Services\TS\MaterialService;
use App\Services\TS\ServiceService;
use App\Services\TS\SubjectService;
use Dpb\Package\Tickets\Models\Ticket;
use Dpb\Package\Tickets\Models\TicketItem;
use Filament\Resources\Pages\Page;
use Illuminate\Database\Eloquent\Model;

class ViewTicketItemPage extends Page
{
    protected static string $resource = TicketItemResource::class;

    protected static string $view = 'filament.resources.ts.ticket-item-resource.pages.view-ticket-item-page';

    public TicketItem $ticketItem;
    public ?TicketAssignment $ticketAssignment;
    // public $activities;
    public $totalExpectedDuration;
    public $totalDuration;
    public $totalMaterialExpenses;
    public $totalServiceExpenses;
    public $materials;
    public $services;
    public $workIntervals;
    public $workAssignments;

    // public function __construct(private TicketItem $ticketItemRepo) 
    // {
    // }

    public function getHeading(): string
    {
        return 'Podzákzka: ' . $this->ticketItem->id . ' - ' . $this->ticketItem->title;
    }

    public function mount(
        TicketItem $ticketItemRepo,
        TicketAssignment $ticketAssignmentRepo,
        ActivityAssignment $activityAssignmentRepo,
        int $record
    ): void {
        // $this->ticketItem = TicketItem::findOrFail($record)->first();
        // $this->ticketItem = TicketItem::findOrFail($record);
        $this->ticketItem = $ticketItemRepo->findOrFail($record);

        // $this->ticketAssignment = app(HeaderService::class)->getHeader($this->ticket);
        $this->ticketAssignment = $ticketAssignmentRepo->whereBelongsTo($this->ticketItem->ticket, 'ticket')->first();

        // $activitySvc = app(ActivityService::class);
        // $this->activities = $activitySvc->getActivities($this->ticketItem);
        // $this->totalExpectedDuration = $activitySvc->getTotalExpectedDuration($this->ticketItem);
        // $this->activities = $activityAssignmentRepo->whereMorphedTo('subject', $this->ticketItem)
        //     ->with(['activity', 'activity.template'])
        //     ->get()
        //     ->map(fn($assignment) => $assignment->activity);

        // // expense materials
        // $materialsSvc = app(MaterialService::class);
        // $this->materials = $materialsSvc->getMaterials($this->ticketItem);
        // $this->totalMaterialExpenses = $materialsSvc->getTotalMaterialExpenses($this->ticketItem);

        // // expense service
        // $servicesSvc = app(ServiceService::class);
        // $this->services = $servicesSvc->getServices($this->ticketItem);
        // $this->totalServiceExpenses = $servicesSvc->getTotalServiceExpenses($this->ticketItem);

    }
}
