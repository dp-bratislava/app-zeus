<?php

namespace App\Filament\Resources\Fleet\Vehicle\VehicleResource\Pages;

use App\Filament\Resources\Fleet\Vehicle\VehicleResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Contracts\Support\Htmlable;

class EditVehicle extends EditRecord
{
    protected static string $resource = VehicleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // Actions\DeleteAction::make(),
        ];
    }

    public function getTitle(): string | Htmlable
    {
        return __('fleet/vehicle.update_heading', ['title' => $this->record->code->code]);
    }      
}
