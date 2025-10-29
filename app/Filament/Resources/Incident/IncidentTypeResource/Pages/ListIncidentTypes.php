<?php

namespace App\Filament\Resources\Incident\IncidentTypeResource\Pages;

use App\Filament\Resources\Incident\IncidentTypeResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListIncidentTypes extends ListRecords
{
    protected static string $resource = IncidentTypeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
