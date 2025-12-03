<?php

namespace App\Filament\Resources\TS\TicketResource\Pages;

use App\Filament\Resources\TS\TicketResource;
use App\Models\TicketAssignment;
use App\Services\TicketAssignmentRepository;
use App\Services\TicketRepository;
use App\Services\TS\ActivityService;
use App\Services\TicketService;
use Dpb\DatahubSync\Models\Department;
use Dpb\Package\Vehicles\Models\Vehicle;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Model;

class EditTicket extends EditRecord
{
    protected static string $resource = TicketResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    public function getTitle(): string | Htmlable
    {
        // return __('tickets/ticket.update_heading', ['title' => $this->record->id]);
        return __('tickets/ticket.update_heading', ['title' => $this->record->getTitleAttribute()]);
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

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        $ticketAssignmentRepo = app(TicketAssignmentRepository::class);
        return $ticketAssignmentRepo->update($record, $data);
    }    
}
