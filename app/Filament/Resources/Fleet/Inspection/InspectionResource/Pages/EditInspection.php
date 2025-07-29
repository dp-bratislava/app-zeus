<?php

namespace App\Filament\Resources\Fleet\Inspection\InspectionResource\Pages;

use App\Filament\Resources\Fleet\Inspection\InspectionResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditInspection extends EditRecord
{
    protected static string $resource = InspectionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
