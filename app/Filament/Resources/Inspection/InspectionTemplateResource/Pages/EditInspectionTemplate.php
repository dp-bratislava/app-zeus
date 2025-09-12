<?php

namespace App\Filament\Resources\Inspection\InspectionTemplateResource\Pages;

use App\Filament\Resources\Inspection\InspectionTemplateResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditInspectionTemplate extends EditRecord
{
    protected static string $resource = InspectionTemplateResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
