<?php

namespace App\Filament\Resources\Incident\IncidentResource\Pages;

use App\Filament\Resources\Incident\IncidentResource;
use App\Services\IncidentRepository;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;

class CreateIncident extends CreateRecord
{
    protected static string $resource = IncidentResource::class;

    // public function __construct($id = null, $resource = null, private IncidentRepository $incidentRepository)
    // {
    //     parent::__construct($id, $resource);
    // }

    protected function handleRecordCreation(array $data): Model
    {
        // return $this->incidentService->createIncident($data);
        return app(IncidentRepository::class)->create($data);
    }
}
