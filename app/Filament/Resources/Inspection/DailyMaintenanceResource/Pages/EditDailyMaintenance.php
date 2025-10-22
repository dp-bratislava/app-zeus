<?php

namespace App\Filament\Resources\Inspection\DailyMaintenanceResource\Pages;

use App\Filament\Resources\Inspection\DailyMaintenanceResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditDailyMaintenance extends EditRecord
{
    protected static string $resource = DailyMaintenanceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
