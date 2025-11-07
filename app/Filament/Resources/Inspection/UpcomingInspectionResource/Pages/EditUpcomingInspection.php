<?php

namespace App\Filament\Resources\Inspection\UpcomingInspectionResource\Pages;

use App\Filament\Resources\Inspection\UpcomingInspectionResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditUpcomingInspection extends EditRecord
{
    protected static string $resource = UpcomingInspectionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
