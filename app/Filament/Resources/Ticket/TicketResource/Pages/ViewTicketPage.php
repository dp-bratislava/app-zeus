<?php

namespace App\Filament\Resources\Ticket\TicketResource\Pages;

use App\Filament\Resources\Ticket\TicketResource;
use App\Models\TicketHeader;
use App\Models\TicketSubject;
use App\Services\Activity\Activity\WorkService;
use App\Services\Ticket\ActivityService;
use App\Services\Ticket\HeaderService;
use App\Services\Ticket\MaterialService;
use App\Services\Ticket\ServiceService;
use App\Services\Ticket\SubjectService;
use Dpb\Package\Tickets\Models\Ticket;
use Filament\Resources\Pages\Page;
use Illuminate\Database\Eloquent\Model;

class ViewTicketPage extends Page
{
    protected static string $resource = TicketResource::class;

    protected static string $view = 'filament.resources.ticket.ticket-resource.pages.view-ticket-page';

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
