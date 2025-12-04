<?php

namespace App\Filament\Resources\TS\TicketResource\Pages;

use App\Filament\Resources\TS\TicketResource;
use App\Services\TS\ActivityService;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use Illuminate\Contracts\Support\Htmlable;

class ViewTicket extends ViewRecord
{
    protected static string $resource = TicketResource::class;

    public function getTitle(): string | Htmlable
    {
        // return __('tickets/ticket.update_heading', ['title' => $this->record->id]);
        return '';
    }  
    
    protected function mutateFormDataBeforeFill(array $data): array
    {
        // $data['subject_id'] = app(TicketAssignment::class)->whereBelongsTo($this->record)->first()?->subject?->id;

        // ticket group
        $data['ticket']['date'] = $this->record->ticket->date;
        $data['ticket']['group_id'] = $this->record->ticket->group_id;

        // activities
        $activities = app(ActivityService::class)->getActivities($this->record->ticket)->toArray();
        $data['activities'] = $activities;
        // dd($activities);
        return $data;
    }
}
