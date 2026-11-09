<?php

namespace Dpb\Modules\Inspections\Filament\Resources\Inspections\UpcomingInspectionResource\Pages;

use Dpb\Modules\Inspections\Filament\Resources\Inspections\UpcomingInspectionResource;
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
