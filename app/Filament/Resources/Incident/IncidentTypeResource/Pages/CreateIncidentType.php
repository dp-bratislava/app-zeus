<?php

namespace App\Filament\Resources\Incident\IncidentTypeResource\Pages;

use App\Filament\Resources\Incident\IncidentTypeResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Contracts\Support\Htmlable;

class CreateIncidentType extends CreateRecord
{
    protected static string $resource = IncidentTypeResource::class;

    public function getTitle(): string | Htmlable
    {
        return __('incidents/incident-type.create_heading');
    }        
}
