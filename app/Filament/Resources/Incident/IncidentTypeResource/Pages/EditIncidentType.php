<?php

namespace App\Filament\Resources\Incident\IncidentTypeResource\Pages;

use App\Filament\Resources\Incident\IncidentTypeResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Contracts\Support\Htmlable;

class EditIncidentType extends EditRecord
{
    protected static string $resource = IncidentTypeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    public function getTitle(): string | Htmlable
    {
        return __('incidents/incident-type.update_heading', ['title' => $this->record->title]);
    }        
}
