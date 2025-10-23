<?php

namespace App\Filament\Resources\Inspection\InspectionTemplateGroupResource\Pages;

use App\Filament\Resources\Inspection\InspectionTemplateGroupResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditInspectionTemplateGroup extends EditRecord
{
    protected static string $resource = InspectionTemplateGroupResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
