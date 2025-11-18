<?php

namespace App\Filament\Resources\Incident\IncidentResource\Pages;

use App\Filament\Resources\Incident\IncidentResource;
use App\Services\IncidentAssignmentRepository;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Model;

class EditIncident extends EditRecord
{
    protected static string $resource = IncidentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    public function getTitle(): string | Htmlable
    {
        return __('incidents/incident.update_heading', ['title' => $this->record->id]);
    }  
    
    protected function mutateFormDataBeforeFill(array $data): array
    {
        $data['subject_id'] = $this->record->subject_id;
        $data['incident']['date'] = $this->record->incident->date;
        $data['incident']['description'] = $this->record->incident->description;
        $data['incident']['type_id'] = $this->record->incident->type_id;
        // dd($data);
        return $data;
    }

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        $incidentAssignment = app(IncidentAssignmentRepository::class)->update($record, $data);

        return $incidentAssignment;
    }
}
