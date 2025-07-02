<?php

namespace App\Filament\Resources\Attendance\ShiftTemplateResource\Pages;

use App\Filament\Resources\Attendance\ShiftTemplateResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListShiftTemplates extends ListRecords
{
    protected static string $resource = ShiftTemplateResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->createAnother(),
        ];
    }
}
