<?php

namespace App\Filament\Resources\Incident\IncidentResource\Pages;

use App\Filament\Resources\Incident\IncidentResource;
use App\Models\IncidentAssignment;
use App\Services\IncidentRepository;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
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

    protected function mutateFormDataBeforeFill(array $data): array
    {
        $data['subject_id'] = app(IncidentAssignment::class)->whereBelongsTo($this->record)->first()->subject->id;
        // dd($data);
        return $data;
    }

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        $incident = app(IncidentRepository::class)->update($record, $data);

        return $incident;
    }
}
