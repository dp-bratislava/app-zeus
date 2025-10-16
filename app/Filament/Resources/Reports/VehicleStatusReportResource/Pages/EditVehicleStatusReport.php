<?php

namespace App\Filament\Resources\Reports\VehicleStatusReportResource\Pages;

use App\Filament\Resources\Reports\VehicleStatusReportResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditVehicleStatusReport extends EditRecord
{
    protected static string $resource = VehicleStatusReportResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
