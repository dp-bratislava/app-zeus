<?php

namespace App\Filament\Resources\Inspection\InspectionResource\Pages;

use App\Filament\Resources\Inspection\InspectionResource;
use App\Services\Inspection\AssignmentService;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Model;

class EditInspection extends EditRecord
{
    protected static string $resource = InspectionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    public function getTitle(): string | Htmlable
    {
        return __('inspections/inspection.update_heading', ['title' => $this->record->id]);
    }  
    
    protected function mutateFormDataBeforeFill(array $data): array
    {
        // $subjectId = TicketAssignment::whereBelongsTo($this->record->ticket)->first()?->subject?->id;
        // $data['subject_id'] = $subjectId;

        // $activities = ActivityAssignment::whereMorphedTo('subject', $this->record->ticketItem)
        //     ->with(['activity', 'activity.template'])
        //     ->get()
        //     ->map(fn($assignment) => $assignment->activity);
        // $data['activities'] = $activities;

        // assigned to
        // $assignedToId = TicketItemAssignment::whereBelongsTo($this->record, 'ticketItem')->first()?->assignedTo?->id;
        // $data['assigned_to'] = $assignedToId;

        $data['inspection_template'] = $this->record->inspection->template_id;
        $data['inspection_date'] = $this->record->inspection->date;
        // dd($data);
        return $data;
    }

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        $assignmentSvc = app(AssignmentService::class);
        $result = $assignmentSvc->update($record, $data);

        return $result;
    }    
}
