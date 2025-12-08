<?php

namespace App\Filament\Resources\Incident\IncidentResource\Pages;

use App\Filament\Resources\Incident\IncidentResource;
use App\Services\IncidentAssignmentRepository;
use App\UseCases\IncidentAssignement\CreateIncidentAssignmentUseCase;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Model;

class CreateIncident extends CreateRecord
{
    protected static string $resource = IncidentResource::class;

    public function getTitle(): string | Htmlable
    {
        return __('incidents/incident.create_heading');
    } 

    protected function handleRecordCreation(array $data): Model
    {
        // return $this->incidentService->createIncident($data);
        return app(CreateIncidentAssignmentUseCase::class)->execute($data);
    }
    // protected function handleRecordCreation(array $data): Model
    // {
    //     // return $this->incidentService->createIncident($data);
    //     return app(IncidentAssignmentRepository::class)->create($data);
    // }
}
