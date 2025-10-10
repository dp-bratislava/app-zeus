<?php

namespace App\Filament\Resources\Ticket\TicketResource\Pages;

use App\Filament\Resources\Ticket\TicketResource;
use App\Models\TicketHeader;
use App\Models\TicketSubject;
use App\Services\Activity\Activity\WorkService;
use App\Services\Ticket\ActivityService;
use App\Services\Ticket\HeaderService;
use App\Services\Ticket\SubjectService;
use Dpb\Package\Tickets\Models\Ticket;
use Filament\Resources\Pages\Page;
use Illuminate\Database\Eloquent\Model;

class ViewTicketPage extends Page
{
    protected static string $resource = TicketResource::class;

    protected static string $view = 'filament.resources.ticket.ticket-resource.pages.view-ticket-page';

    public Ticket $ticket;
    public TicketHeader $ticketHeader;
    public ?Model $ticketSubject;
    public $activities;
    public $workIntervals;
    public $workAssignments;

    public function mount(int $record): void
    {
        $this->ticket = Ticket::findOrFail($record);
        $this->ticketHeader = app(HeaderService::class)->getHeader($this->ticket);
        $this->ticketSubject = app(SubjectService::class)->getSubject($this->ticket);

        $this->activities = app(ActivityService::class)->getActivities($this->ticket);
        $this->workIntervals = [];
        $wiSvc = app(WorkService::class);
        foreach ($this->activities as $activity) {
            $this->workIntervals[$activity->id] = $wiSvc->getWorkIntervals($activity);            
        }

        $this->workAssignments = [];
        foreach ($this->activities as $activity) {
            $this->workAssignments[$activity->id] = $wiSvc->getWorkAssignments($activity);            
        }        
    }    
}
