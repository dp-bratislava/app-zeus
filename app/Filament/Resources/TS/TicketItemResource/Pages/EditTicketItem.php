<?php

namespace App\Filament\Resources\TS\TicketItemResource\Pages;

use App\Filament\Resources\TS\TicketItemResource;
use App\Models\ActivityAssignment;
use App\Models\TicketAssignment;
use App\Models\TicketItemAssignment;
use App\Services\TicketItemRepository;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;

class EditTicketItem extends EditRecord
{
    protected static string $resource = TicketItemResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        $subjectId = TicketAssignment::whereBelongsTo($this->record->ticket)->first()?->subject?->id;
        $data['subject_id'] = $subjectId;

        $activities = ActivityAssignment::whereMorphedTo('subject', $this->record)
            ->with(['activity', 'activity.template'])
            ->get()
            ->map(fn($assignment) => $assignment->activity);
        $data['activities'] = $activities;

        // assigned to
        $assignedToId = TicketItemAssignment::whereBelongsTo($this->record, 'ticketItem')->first()?->assignedTo?->id;
        $data['assigned_to'] = $assignedToId;

        // dd($data);
        return $data;
    }

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        $ticketItemRepo = app(TicketItemRepository::class);
        $result = $ticketItemRepo->update($record, $data);

        return $result;
    }
}
