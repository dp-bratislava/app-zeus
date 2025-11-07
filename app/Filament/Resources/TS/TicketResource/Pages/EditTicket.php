<?php

namespace App\Filament\Resources\TS\TicketResource\Pages;

use App\Filament\Resources\TS\TicketResource;
use App\Models\TicketAssignment;
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
        return __('tickets/ticket.form.update_heading', ['title' => $this->record->title]);
    }  
    
    protected function mutateFormDataBeforeFill(array $data): array
    {
        // $departmentId = app(TicketService::class)->getDepartment($this->record)?->id;

        // $data['department_id'] = $departmentId;
        $data['subject_id'] = app(TicketAssignment::class)->whereBelongsTo($this->record)->first()?->subject?->id;

        $activities = app(ActivityService::class)->getActivities($this->record)->toArray();

        $data['activities'] = $activities;
        // dd($activities);
        return $data;
    }

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        $ticketRepo = app(TicketRepository::class);
        $result = $ticketRepo->update($record, $data);

        return $result;
    }    
}
