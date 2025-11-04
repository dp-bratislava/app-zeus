<?php

namespace App\Filament\Resources\TS\TicketResource\Pages;

use App\Filament\Resources\TS\TicketResource;
use App\Models\TicketHeader;
use App\Models\TicketSubject;
use App\Services\Activity\Activity\WorkService;
use App\Services\TS\ActivityService;
use App\Services\TS\HeaderService;
use App\Services\TS\MaterialService;
use App\Services\TS\ServiceService;
use App\Services\TS\SubjectService;
use Dpb\Package\Tickets\Models\Ticket;
use Filament\Resources\Pages\Page;
use Illuminate\Database\Eloquent\Model;

class ViewTicketPage extends Page
{
    protected static string $resource = TicketResource::class;

    protected static string $view = 'filament.resources.ts.ticket-resource.pages.view-ticket-page';

    public Ticket $ticket;
    public ?TicketHeader $ticketHeader;
    public ?Model $ticketSubject;
    public $activities;
    public $totalExpectedDuration;
    public $totalDuration;
    public $totalMaterialExpenses;
    public $totalServiceExpenses;
    public $materials;
    public $services;
    public $workIntervals;
    public $workAssignments;

    public function mount(int $record): void
    {
        $this->ticket = Ticket::findOrFail($record);
        $this->ticketHeader = app(HeaderService::class)->getHeader($this->ticket);
        $this->ticketSubject = app(SubjectService::class)->getSubject($this->ticket);

        $activitySvc = app(ActivityService::class);
        $this->activities = $activitySvc->getActivities($this->ticket);
        $this->totalExpectedDuration = $activitySvc->getTotalExpectedDuration($this->ticket);

        // expense materials
        $materialsSvc = app(MaterialService::class);
        $this->materials = $materialsSvc->getMaterials($this->ticket);
        $this->totalMaterialExpenses = $materialsSvc->getTotalMaterialExpenses($this->ticket);

        // expense service
        $servicesSvc = app(ServiceService::class);
        $this->services = $servicesSvc->getServices($this->ticket);
        $this->totalServiceExpenses = $servicesSvc->getTotalServiceExpenses($this->ticket);

        // work intervals
        $this->workIntervals = [];
        $wiSvc = app(WorkService::class);
        $this->totalDuration = 0;
        foreach ($this->activities as $activity) {
            $this->workIntervals[$activity->id] = $wiSvc->getWorkIntervals($activity);            
            $this->totalDuration += $wiSvc->getTotalDuration($activity);
        }

        // work assignemnts
        $this->workAssignments = [];
        foreach ($this->activities as $activity) {
            $this->workAssignments[$activity->id] = $wiSvc->getWorkAssignments($activity);            
        }        
    }    
}
