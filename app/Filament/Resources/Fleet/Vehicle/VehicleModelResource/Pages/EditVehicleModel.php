<?php

namespace App\Filament\Resources\Fleet\Vehicle\VehicleModelResource\Pages;

use App\Filament\Resources\Fleet\Vehicle\VehicleModelResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Contracts\Support\Htmlable;

class EditVehicleModel extends EditRecord
{
    protected static string $resource = VehicleModelResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    public function getTitle(): string | Htmlable
    {
        return __('fleet/vehicle-model.form.update_heading', ['title' => $this->record->title]);
    }      
}
